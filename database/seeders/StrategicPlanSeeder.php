<?php

namespace Database\Seeders;

use App\Models\StrategicPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StrategicPlanSeeder extends Seeder
{
    /**
     * Kaynak: https://kirklareli.bel.tr/diger-duyurular/25
     *
     * Page titles were extracted from the page. Download URLs were resolved
     * from public strategic plan archive pages where available.
     *
     * @var array<int, array{
     *     title: string,
     *     year: int,
     *     file_url: string|null,
     *     source_url: string,
     *     note?: string
     * }>
     */
    private array $plans = [
        [
            'title' => '2025-2029 STRATEJİK PLAN',
            'year' => 2025,
            'file_url' => 'http://www.sp.gov.tr/upload/xSPStratejikPlan/files/kKwDo+Kirklareli_Belediyesi_Stratejik_Plan_2025_2029.pdf',
            'source_url' => 'https://kirklareli.bel.tr/diger-duyurular/25',
        ],
        [
            'title' => '2022-2024 STRATEJİK PLAN (REVİZE)',
            'year' => 2022,
            'file_url' => null,
            'source_url' => 'https://kirklareli.bel.tr/diger-duyurular/25',
            'note' => 'Kaynak sayfada listeleniyor; doğrudan PDF bağlantısı tespit edilemedi.',
        ],
        [
            'title' => '2020-2024 STRATEJİK PLAN',
            'year' => 2020,
            'file_url' => 'http://www.sp.gov.tr/upload/xSPStratejikPlan/files/BeKLg+Kirklareli_Belediyesi_2020_2024_Stratejik_Plani.pdf',
            'source_url' => 'https://kirklareli.bel.tr/diger-duyurular/25',
        ],
        [
            'title' => '2015-2019 STRATEJİK PLAN (REVİZE)',
            'year' => 2015,
            'file_url' => null,
            'source_url' => 'https://kirklareli.bel.tr/diger-duyurular/25',
            'note' => 'Kaynak sayfada listeleniyor; doğrudan PDF bağlantısı tespit edilemedi.',
        ],
        [
            'title' => '2015-2019 STRATEJİK PLAN',
            'year' => 2015,
            'file_url' => 'http://www.sp.gov.tr/upload/xSPStratejikPlan/files/2ANaS+Kirklareli_15-19_SP.pdf',
            'source_url' => 'https://kirklareli.bel.tr/diger-duyurular/25',
        ],
        [
            'title' => '2009-2014 STRATEJİK PLAN',
            'year' => 2009,
            'file_url' => 'http://www.sp.gov.tr/upload/xSPStratejikPlan/files/s60qu+Kirklareli_10-14_SP.pdf',
            'source_url' => 'https://kirklareli.bel.tr/diger-duyurular/25',
            'note' => 'Arşivde en yakın karşılık 2010-2014 dönemi dosyasıdır.',
        ],
    ];

    public function run(): void
    {
        foreach ($this->plans as $plan) {
            $title = $plan['title'];
            $sourceUrl = $plan['source_url'];
            $fileUrl = $plan['file_url'];
            $slug = $this->makeStableSlug($title, $sourceUrl);
            $filePath = null;

            if ($fileUrl) {
                $tempFile = storage_path('app/tmp-' . md5($fileUrl) . '.pdf');

                try {
                    $response = Http::timeout(180)
                        ->retry(2, 500)
                        ->sink($tempFile)
                        ->get($fileUrl);

                    if ($response->successful() && file_exists($tempFile)) {
                        $filePath = 'strategic-plans/' . $slug . '.pdf';
                        Storage::disk('public')->put($filePath, fopen($tempFile, 'r'));
                    }
                } catch (\Throwable $e) {
                    $this->command?->warn("İndirilemedi: {$title} - {$e->getMessage()}");
                } finally {
                    if (isset($tempFile) && file_exists($tempFile)) {
                        @unlink($tempFile);
                    }
                }
            }

            StrategicPlan::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'year' => $plan['year'],
                    'file_path' => $filePath,
                    'source_url' => $sourceUrl,
                    'note' => $plan['note'] ?? null,
                    'is_active' => true,
                ]
            );

            $this->command?->info("Kaydedildi: {$title}");
        }
    }

    private function makeStableSlug(string $title, string $sourceUrl): string
    {
        $base = Str::slug($title);
        $suffix = substr(md5($sourceUrl . '|' . $title), 0, 6);

        return "{$base}-{$suffix}";
    }
}

