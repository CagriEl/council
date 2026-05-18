<?php

namespace Database\Seeders;

use App\Models\ActivityReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivityReportSeeder extends Seeder
{
    /**
     * @var array<int, array{title: string, url: string}>
     */
    private array $reports = [
        ['title' => '2025 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/Files/Dokuman/505e0bea99f341b9b99a39380018ba64.pdf'],
        ['title' => '2024 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/Files/Dokuman/ea40f9e316fe4955920a8e1d44bd1233.pdf'],
        ['title' => '2023 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2023-faaliyet-raporu.pdf'],
        ['title' => '2022 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2022-yili-faaliyet-raporu.pdf'],
        ['title' => '2021 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2021-yili-faaliyet-raporu.pdf'],
        ['title' => '2020 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2020-yili-faaliyet-raporu.pdf'],
        ['title' => '2019 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2019-yili-faaliyet-raporu.pdf'],
        ['title' => '2018 YILI FAALİYET RAPORLARI 2. BÖLÜM', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2018-yili-faaliyet-raporu-bolum2.pdf'],
        ['title' => '2018 YILI FAALİYET RAPORLARI 1. BÖLÜM', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2018-yili-faaliyet-raporu-bolum1.pdf'],
        ['title' => '2017 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2017-yili-faaliyet-raporu.pdf'],
        ['title' => '2016 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2016-yili-faaliyet-raporu.pdf'],
        ['title' => '2015 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2015-yili-faaliyet-raporu.pdf'],
        ['title' => '2014 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2014-yili-faaliyet-raporu.pdf'],
        ['title' => '2013 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2013-yili-faaliyet-raporu.pdf'],
        ['title' => '2012 YILI FAALİYET RAPORLARI', 'url' => 'https://api.kirklarelibelediyesi.com/files/faalyet/2012-yili-faaliyet-raporu.pdf'],
    ];

    public function run(): void
    {
        foreach ($this->reports as $report) {
            $title = trim($report['title']);
            $sourceUrl = $report['url'];
            $tempFile = storage_path('app/tmp-' . md5($sourceUrl) . '.pdf');

            try {
                $response = Http::timeout(300)
                    ->retry(2, 500)
                    ->sink($tempFile)
                    ->get($sourceUrl);

                if (! $response->successful() || ! file_exists($tempFile)) {
                    $this->command?->warn("Atlandı (indirilemedi): {$title}");
                    continue;
                }

                $year = $this->extractYear($title);
                $slug = $this->makeStableSlug($title, $sourceUrl);
                $filename = $slug . '.pdf';
                $storagePath = 'activity-reports/' . $filename;

                Storage::disk('public')->put($storagePath, fopen($tempFile, 'r'));

                ActivityReport::updateOrCreate(
                    ['source_url' => $sourceUrl],
                    [
                        'title' => $title,
                        'year' => $year,
                        'slug' => $slug,
                        'file_path' => $storagePath,
                        'is_active' => true,
                    ]
                );

                $this->command?->info("Kaydedildi: {$title}");
            } catch (\Throwable $e) {
                $this->command?->warn("Atlandı (hata): {$title} - {$e->getMessage()}");
            } finally {
                if (file_exists($tempFile)) {
                    @unlink($tempFile);
                }
            }
        }
    }

    private function extractYear(string $title): ?int
    {
        if (preg_match('/(20\d{2})/', $title, $matches) === 1) {
            return (int) $matches[1];
        }

        return null;
    }

    private function makeStableSlug(string $title, string $sourceUrl): string
    {
        $base = Str::slug($title);
        $suffix = substr(md5($sourceUrl), 0, 6);

        return "{$base}-{$suffix}";
    }
}

