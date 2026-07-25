<?php

namespace App\Console\Commands;

use App\Models\ActivityReport;
use App\Models\Announcement;
use App\Models\StrategicPlan;
use App\Support\Transparency\TransparencySections;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MirrorLegacyApiAssets extends Command
{
    protected $signature = 'import:mirror-legacy-api {--dry-run : Sadece listele}';

    protected $description = 'api.kirklarelibelediyesi.com URL’lerini storage/legacy altına indirir ve DB/JSON referanslarını yerelleştirir';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $urls = collect();

        // Transparency JSON
        foreach (TransparencySections::all() as $section) {
            foreach ($section['documents'] ?? [] as $doc) {
                if ($this->isLegacyApiUrl($doc['url'] ?? '')) {
                    $urls->push($doc['url']);
                }
            }
        }

        // Activity + strategic source_url if still remote (file may already be local)
        ActivityReport::query()->each(function (ActivityReport $r) use ($urls) {
            if ($this->isLegacyApiUrl((string) $r->source_url)) {
                $urls->push($r->source_url);
            }
            if ($this->isLegacyApiUrl((string) $r->file_path)) {
                $urls->push($r->file_path);
            }
        });

        StrategicPlan::query()->each(function (StrategicPlan $r) use ($urls) {
            if ($this->isLegacyApiUrl((string) $r->source_url)) {
                $urls->push($r->source_url);
            }
            if ($this->isLegacyApiUrl((string) ($r->file_path ?? ''))) {
                $urls->push($r->file_path);
            }
        });

        // Announcement file_path if remote
        Announcement::query()->whereNotNull('file_path')->each(function (Announcement $a) use ($urls) {
            if ($this->isLegacyApiUrl((string) $a->file_path)) {
                $urls->push($a->file_path);
            }
        });

        $urls = $urls->filter()->unique()->values();
        $this->info('Bulunan legacy API URL: '.$urls->count());

        $map = [];
        foreach ($urls as $url) {
            $local = $this->mirrorUrl($url, $dryRun);
            if ($local) {
                $map[$url] = $local;
                $this->line(($dryRun ? '[dry] ' : '').$url.' → '.$local);
            }
        }

        if ($dryRun || $map === []) {
            return self::SUCCESS;
        }

        $this->rewriteTransparency($map);
        $this->rewriteActivityReports($map);
        $this->rewriteStrategicPlans($map);
        $this->rewriteAnnouncements($map);

        Storage::disk('local')->put(
            'migration/legacy-url-map.json',
            json_encode($map, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->info('Mirror + rewrite tamamlandı. Eşleme: storage/app/private/migration/legacy-url-map.json (local disk)');

        return self::SUCCESS;
    }

    private function isLegacyApiUrl(string $url): bool
    {
        return str_contains(Str::lower($url), 'api.kirklarelibelediyesi.com');
    }

    private function mirrorUrl(string $url, bool $dryRun): ?string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $path = ltrim($path, '/');
        // Normalize to legacy/{original-path}
        $relative = 'legacy/'.preg_replace('#^files/#i', 'Files/', $path);
        $relative = preg_replace('#Dokuman#i', 'Dokuman', $relative) ?? $relative;

        if ($dryRun) {
            return $relative;
        }

        if (Storage::disk('public')->exists($relative) && Storage::disk('public')->size($relative) > 1000) {
            return $relative;
        }

        try {
            $response = Http::timeout(180)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; KirklareliImporter/1.0)'])
                ->retry(2, 800)
                ->get($url);

            if (! $response->successful()) {
                $this->warn("İndirilemedi ({$response->status()}): {$url}");

                return null;
            }

            Storage::disk('public')->put($relative, $response->body());

            return $relative;
        } catch (\Throwable $e) {
            $this->warn("Hata: {$url} — {$e->getMessage()}");

            return null;
        }
    }

    /** @param  array<string, string>  $map */
    private function rewriteTransparency(array $map): void
    {
        $sections = TransparencySections::all()->map(function (array $section) use ($map) {
            $section['documents'] = collect($section['documents'] ?? [])
                ->map(function (array $doc) use ($map) {
                    $url = $doc['url'] ?? '';
                    if (isset($map[$url])) {
                        $doc['url'] = Storage::disk('public')->url($map[$url]);
                        $doc['file_path'] = $map[$url];
                    }

                    return $doc;
                })
                ->all();

            return $section;
        })->all();

        TransparencySections::save($sections);
    }

    /** @param  array<string, string>  $map */
    private function rewriteActivityReports(array $map): void
    {
        ActivityReport::query()->each(function (ActivityReport $r) use ($map) {
            $dirty = false;
            if (isset($map[(string) $r->source_url])) {
                // Keep source_url as original for uniqueness; ensure file_path local
                if (! filled($r->file_path) || $this->isLegacyApiUrl((string) $r->file_path)) {
                    $r->file_path = $map[(string) $r->source_url];
                    $dirty = true;
                }
            }
            if (isset($map[(string) $r->file_path])) {
                $r->file_path = $map[(string) $r->file_path];
                $dirty = true;
            }
            if ($dirty) {
                $r->save();
            }
        });
    }

    /** @param  array<string, string>  $map */
    private function rewriteStrategicPlans(array $map): void
    {
        StrategicPlan::query()->each(function (StrategicPlan $r) use ($map) {
            $dirty = false;
            if (isset($map[(string) $r->source_url])) {
                if (! filled($r->file_path) || $this->isLegacyApiUrl((string) ($r->file_path ?? ''))) {
                    $r->file_path = $map[(string) $r->source_url];
                    $dirty = true;
                }
            }
            if (isset($map[(string) ($r->file_path ?? '')])) {
                $r->file_path = $map[(string) $r->file_path];
                $dirty = true;
            }
            if ($dirty) {
                $r->save();
            }
        });
    }

    /** @param  array<string, string>  $map */
    private function rewriteAnnouncements(array $map): void
    {
        Announcement::query()->whereNotNull('file_path')->each(function (Announcement $a) use ($map) {
            $path = (string) $a->file_path;
            if (isset($map[$path])) {
                $a->file_path = $map[$path];
                $a->save();
            }
        });
    }
}
