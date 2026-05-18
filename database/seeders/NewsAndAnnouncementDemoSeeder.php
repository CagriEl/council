<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class NewsAndAnnouncementDemoSeeder extends Seeder
{
    /** @var list<string> */
    private array $imagePool = [];

    public function run(): void
    {
        News::where('slug', 'like', 'ornek-haber-%')->delete();
        Announcement::where('slug', 'like', 'ornek-ilan-%')->delete();

        $this->imagePool = $this->buildImagePool(24);

        $newsCategories = [
            'belediye' => 'Belediye',
            'kultur' => 'Kültür ve Sanat',
            'spor' => 'Spor',
            'cevre' => 'Çevre ve Kent',
            'sosyal' => 'Sosyal Hizmetler',
        ];

        $announcementTypes = [
            'duyuru' => 'Genel duyuru',
            'resmi' => 'Resmi ilan',
            'ihale' => 'İhale duyurusu',
        ];

        $imgCounter = 0;
        foreach ($newsCategories as $catKey => $catLabel) {
            for ($n = 1; $n <= 15; $n++) {
                $imgCounter++;
                $slug = 'ornek-haber-'.$catKey.'-'.str_pad((string) $n, 2, '0', STR_PAD_LEFT);
                $path = $this->storeSampleImage('news-covers', $slug, $imgCounter);

                News::create([
                    'title' => "{$catLabel} — Örnek haber başlığı {$n}",
                    'slug' => $slug,
                    'category' => $catKey,
                    'summary' => "Kırklareli Belediyesi {$catLabel} alanındaki çalışmalara ilişkin özet bilgi (örnek içerik).",
                    'content' => '<p>Bu metin <strong>örnek haber içeriğidir</strong>. Tasarım ve listelemeleri test etmek için üretilmiştir.</p><p>Güncel bilgi için resmî duyuruları takip ediniz.</p>',
                    'image_path' => $path,
                    'published_at' => Carbon::now()->subDays(150 - min($imgCounter, 120))->toDateString(),
                    'is_headline' => $n === 1,
                    'is_active' => true,
                ]);
            }
        }

        $annCounter = 0;
        foreach ($announcementTypes as $typeKey => $typeLabel) {
            for ($n = 1; $n <= 15; $n++) {
                $annCounter++;
                $slug = 'ornek-ilan-'.$typeKey.'-'.str_pad((string) $n, 2, '0', STR_PAD_LEFT);
                $path = $this->storeSampleImage('announcements/covers', $slug, $annCounter + 400);

                $annDate = Carbon::now()->subDays(100 - $annCounter)->toDateString();
                Announcement::create([
                    'title' => "{$typeLabel} — Örnek duyuru {$n}",
                    'slug' => $slug,
                    'content' => "<p>Örnek <em>{$typeLabel}</em> metnidir. Resmî bilgi için ilgili birim ile görüşünüz.</p>",
                    'type' => $typeKey,
                    'date' => $annDate,
                    'published_at' => $annDate,
                    'unpublished_at' => null,
                    'file_path' => null,
                    'image_path' => $path,
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * @return list<string>
     */
    private function buildImagePool(int $targetCount): array
    {
        $pool = [];
        for ($i = 1; $i <= $targetCount; $i++) {
            try {
                $response = Http::timeout(12)->get('https://picsum.photos/seed/kirklareli-'.$i.'/800/450');
                if ($response->successful() && strlen($response->body()) > 2000) {
                    $pool[] = $response->body();
                }
            } catch (\Throwable) {
                break;
            }
        }

        $fallbacks = [];
        foreach ([public_path('images/logo.png'), public_path('images/atatr.png')] as $p) {
            if (! is_readable($p)) {
                continue;
            }
            $binary = file_get_contents($p);
            if ($binary !== false && $binary !== '') {
                $fallbacks[] = $binary;
            }
        }

        if ($pool === []) {
            if ($fallbacks === []) {
                throw new \RuntimeException('Örnek görseller yüklenemedi: ağ yok ve public/images/logo.png bulunamadı.');
            }

            return $fallbacks;
        }

        return $pool;
    }

    private function storeSampleImage(string $subdir, string $basename, int $index): string
    {
        $relativePath = $subdir.'/'.$basename.'.jpg';
        $body = $this->imagePool[$index % count($this->imagePool)];
        Storage::disk('public')->put($relativePath, $body);

        return $relativePath;
    }
}
