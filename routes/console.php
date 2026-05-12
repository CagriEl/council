<?php

use App\Models\Announcement;
use App\Models\CouncilDecision;
use App\Models\CouncilMember;
use App\Models\PoliticalParty;
use App\Services\AnnouncementScraperService;
use App\Services\CouncilDecisionsScraperService;
use App\Services\TenderAnnouncementScraperService;
use App\Services\WebDuyuruScraperService;
use App\Support\AnnouncementSlug;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sync:ihale-announcements {--url=https://kirklareli.bel.tr/ihale-duyuruları/1} {--limit=200} {--dry-run}', function (TenderAnnouncementScraperService $scraper) {
    $url = (string) $this->option('url');
    $limit = min(max((int) $this->option('limit'), 1), 1000);
    $dryRun = (bool) $this->option('dry-run');

    $items = array_slice($scraper->fetch($url), 0, $limit);
    if ($items === []) {
        $this->warn('Kaynakta işlenecek ihale duyurusu bulunamadı.');

        return self::SUCCESS;
    }

    $created = 0;
    $updated = 0;

    foreach ($items as $item) {
        $title = trim((string) ($item['title'] ?? ''));
        $detailUrl = trim((string) ($item['detail_url'] ?? ''));
        if ($title === '' || $detailUrl === '') {
            continue;
        }
        $title = Str::limit($title, 240, '');

        $legacyBase = Str::slug(Str::limit($title, 80, ''), '-', 'tr');
        $legacySlug = ($legacyBase !== '' ? $legacyBase : 'ihale').'-'.substr(sha1($detailUrl), 0, 10);

        $existing = Announcement::query()->where('slug', $legacySlug)->first()
            ?? Announcement::query()->where('type', 'ihale')->where('title', $title)->first();

        $ignoreId = $existing?->id;
        $slug = AnnouncementSlug::uniqueFromTitle($title, function (string $s) use ($ignoreId): bool {
            return Announcement::query()
                ->where('slug', $s)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists();
        });

        $payload = [
            'title' => $title,
            'type' => 'ihale',
            'slug' => $slug,
            'content' => '<p>İhale duyurusu ayrıntıları panelden düzenlenebilir.</p>',
            'date' => now()->toDateString(),
            'published_at' => now()->toDateString(),
            'is_active' => true,
            'image_path' => null,
            'file_path' => null,
            'unpublished_at' => null,
        ];

        if ($dryRun) {
            if ($existing) {
                $updated++;
            } else {
                $created++;
            }

            continue;
        }

        if ($existing) {
            $existing->fill($payload)->save();
            $updated++;
        } else {
            Announcement::query()->create($payload);
            $created++;
        }
    }

    $this->info("Kaynak: {$url}");
    $this->info('Toplam çekilen: '.count($items));
    $this->info("Yeni: {$created}");
    $this->info("Güncellenen: {$updated}");
    $this->info($dryRun ? 'Dry-run tamamlandı (DB yazılmadı).' : 'İhale duyuruları tabloya kaydedildi.');

    return self::SUCCESS;
})->purpose('Kirklareli sitesindeki ihale duyurularını announcements tablosuna yazar');

Artisan::command('sync:web-duyurular {--url=https://kirklareli.bel.tr/tum-duyurular} {--limit=120} {--dry-run}', function (WebDuyuruScraperService $scraper) {
    $url = (string) $this->option('url');
    $limit = min(max((int) $this->option('limit'), 1), 500);
    $dryRun = (bool) $this->option('dry-run');

    $items = array_slice($scraper->fetchList($url), 0, $limit);
    if ($items === []) {
        $this->warn('Kaynakta işlenecek web duyurusu bulunamadı.');

        return self::SUCCESS;
    }

    $created = 0;
    $updated = 0;
    $failed = 0;

    foreach ($items as $item) {
        $title = trim((string) ($item['title'] ?? ''));
        $detailUrl = trim((string) ($item['detail_url'] ?? ''));
        if ($title === '' || $detailUrl === '') {
            $failed++;

            continue;
        }

        $detail = $scraper->fetchDetail($detailUrl);
        $resolvedTitle = trim((string) ($detail['title'] ?? $title));
        $resolvedTitle = Str::limit($resolvedTitle !== '' ? $resolvedTitle : $title, 240, '');
        $resolvedImageUrl = (string) ($detail['image_url'] ?? $item['image_url'] ?? '');
        $contentHtml = trim((string) ($detail['content_html'] ?? ''));
        if ($contentHtml === '') {
            $contentHtml = '<p>İçerik henüz alınamadı; ayrıntıları yönetim panelinden girebilirsiniz.</p>';
        }

        $slugFromUrl = null;
        $path = (string) parse_url($detailUrl, PHP_URL_PATH);
        if (preg_match('#/duyurular/\d+/([^/]+)$#u', $path, $m) === 1) {
            $slugFromUrl = Str::slug((string) $m[1], '-', 'tr');
        }
        $legacyBase = $slugFromUrl ?: Str::slug(Str::limit($resolvedTitle, 80, ''), '-', 'tr');
        $legacySlug = ($legacyBase !== '' ? $legacyBase : 'duyuru').'-'.substr(sha1($detailUrl), 0, 8);

        $existing = Announcement::query()->where('slug', $legacySlug)->first()
            ?? Announcement::query()->where('type', 'duyuru')->where('title', $resolvedTitle)->first();

        $ignoreId = $existing?->id;
        $slug = AnnouncementSlug::uniqueFromTitle($resolvedTitle, function (string $s) use ($ignoreId): bool {
            return Announcement::query()
                ->where('slug', $s)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists();
        });

        $imagePath = null;
        if ($resolvedImageUrl !== '') {
            try {
                $imgResponse = Http::timeout(20)->get($resolvedImageUrl);
                if ($imgResponse->successful()) {
                    $contentType = (string) $imgResponse->header('Content-Type', '');
                    $ext = 'jpg';
                    if (str_contains($contentType, 'png')) {
                        $ext = 'png';
                    } elseif (str_contains($contentType, 'webp')) {
                        $ext = 'webp';
                    } elseif (str_contains($contentType, 'gif')) {
                        $ext = 'gif';
                    } else {
                        $pathInfo = pathinfo((string) parse_url($resolvedImageUrl, PHP_URL_PATH));
                        $candidate = strtolower((string) ($pathInfo['extension'] ?? ''));
                        if (in_array($candidate, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                            $ext = $candidate === 'jpeg' ? 'jpg' : $candidate;
                        }
                    }
                    $imagePath = 'announcements/imported/'.substr(sha1($detailUrl.$resolvedImageUrl), 0, 20).'.'.$ext;
                    Storage::disk('public')->put($imagePath, $imgResponse->body());
                }
            } catch (\Throwable) {
                $imagePath = null;
            }
        }

        $payload = [
            'title' => $resolvedTitle,
            'type' => 'duyuru',
            'slug' => $slug,
            'content' => $contentHtml,
            'date' => now()->toDateString(),
            'published_at' => now()->toDateString(),
            'is_active' => true,
            'image_path' => $imagePath,
            'file_path' => null,
            'unpublished_at' => null,
        ];

        if ($dryRun) {
            if ($existing) {
                $updated++;
            } else {
                $created++;
            }

            continue;
        }

        if ($existing) {
            $existing->fill($payload)->save();
            $updated++;
        } else {
            Announcement::query()->create($payload);
            $created++;
        }
    }

    $this->info("Kaynak: {$url}");
    $this->info('Toplam çekilen: '.count($items));
    $this->info("Yeni: {$created}");
    $this->info("Güncellenen: {$updated}");
    $this->info("Atlanan/Hata: {$failed}");
    $this->info($dryRun ? 'Dry-run tamamlandı (DB yazılmadı).' : 'Web duyuruları tabloya kaydedildi.');

    return self::SUCCESS;
})->purpose('Tum duyurular kaynağından başlık/görsel/içerik alıp type=duyuru kaydeder');

Artisan::command('sync:resmi-announcements {--url=} {--limit=200} {--dry-run}', function (AnnouncementScraperService $scraper) {
    $urlOpt = (string) $this->option('url');
    $url = $urlOpt !== '' ? $urlOpt : $scraper->defaultScraperUrl();
    $limit = min(max((int) $this->option('limit'), 1), 500);
    $dryRun = (bool) $this->option('dry-run');

    $items = array_slice($scraper->fetchOfficialList($url), 0, $limit);
    if ($items === []) {
        $this->warn('Kaynakta resmî duyuru bulunamadı (URL: '.$url.').');

        return self::SUCCESS;
    }

    $created = 0;
    $updated = 0;
    $failed = 0;

    foreach ($items as $item) {
        $title = Str::limit(trim((string) ($item['title'] ?? '')), 240, '');
        $detailUrl = trim((string) ($item['detail_url'] ?? ''));
        if ($title === '' || $detailUrl === '') {
            $failed++;

            continue;
        }

        $legacySlug = 'resmi-'.substr(sha1($detailUrl), 0, 20);

        $existing = Announcement::query()->where('slug', $legacySlug)->first()
            ?? Announcement::query()->where('type', 'resmi')->where('title', $title)->first();

        $ignoreId = $existing?->id;
        $slug = AnnouncementSlug::uniqueFromTitle($title, function (string $s) use ($ignoreId): bool {
            return Announcement::query()
                ->where('slug', $s)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists();
        });

        $filePath = null;
        $detailLower = mb_strtolower($detailUrl);
        if (str_contains($detailLower, '.pdf') || str_contains($detailLower, '/files/') || str_contains($detailLower, 'dokuman')) {
            try {
                $pdfResponse = Http::timeout(45)
                    ->withHeaders([
                        'User-Agent' => 'KirklareliResmiAnnouncementSync/1.0',
                        'Accept' => 'application/pdf,*/*',
                    ])
                    ->get($detailUrl);
                if ($pdfResponse->successful()) {
                    $body = $pdfResponse->body();
                    $ct = mb_strtolower((string) $pdfResponse->header('Content-Type', ''));
                    $looksPdf = str_starts_with($body, '%PDF')
                        || str_contains($ct, 'pdf')
                        || (str_contains($ct, 'octet-stream') && str_contains($detailLower, '.pdf'));
                    if ($looksPdf && $body !== '') {
                        $filePath = 'announcements/resmi/'.substr(sha1($detailUrl), 0, 24).'.pdf';
                        Storage::disk('public')->put($filePath, $body);
                    }
                }
            } catch (\Throwable) {
                $filePath = null;
            }
        }

        $content = '<p>Resmî duyuru metni PDF belgesinde yer almaktadır.</p>';

        $payload = [
            'title' => $title,
            'type' => 'resmi',
            'slug' => $slug,
            'content' => $content,
            'date' => now()->toDateString(),
            'published_at' => now()->toDateString(),
            'is_active' => true,
            'image_path' => null,
            'file_path' => $filePath,
            'unpublished_at' => null,
        ];

        if ($dryRun) {
            if ($existing) {
                $updated++;
            } else {
                $created++;
            }

            continue;
        }

        if ($existing) {
            $payload['date'] = $existing->date;
            $payload['published_at'] = $existing->published_at ?? $existing->date;
            if ($filePath === null) {
                $payload['file_path'] = $existing->file_path;
            }
            $existing->fill($payload)->save();
            $updated++;
        } else {
            Announcement::query()->create($payload);
            $created++;
        }
    }

    $this->info("Kaynak: {$url}");
    $this->info('İşlenen: '.count($items));
    $this->info("Yeni: {$created}");
    $this->info("Güncellenen: {$updated}");
    $this->info("Atlanan: {$failed}");
    $this->info($dryRun ? 'Dry-run (yazılmadı).' : 'Resmî duyurular announcements tablosuna işlendi (/duyurular?tip=resmi).');

    return self::SUCCESS;
})->purpose('kirklareli.bel.tr resmi-duyurular listesini çekip type=resmi olarak kaydeder');

Artisan::command('sync:meclis-kararlari {--start=2019} {--end=2025} {--dry-run}', function (CouncilDecisionsScraperService $scraper) {
    $start = (int) $this->option('start');
    $end = (int) $this->option('end');
    $dryRun = (bool) $this->option('dry-run');

    if ($start <= 0 || $end <= 0 || $start > $end) {
        $this->error('Geçersiz yıl aralığı. Örn: --start=2019 --end=2025');

        return self::FAILURE;
    }

    $monthMap = [
        'ocak' => 1,
        'subat' => 2,
        'şubat' => 2,
        'mart' => 3,
        'nisan' => 4,
        'mayis' => 5,
        'mayıs' => 5,
        'haziran' => 6,
        'temmuz' => 7,
        'agustos' => 8,
        'ağustos' => 8,
        'eylul' => 9,
        'eylül' => 9,
        'ekim' => 10,
        'kasim' => 11,
        'kasım' => 11,
        'aralik' => 12,
        'aralık' => 12,
    ];
    $monthLabelByNum = [1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan', 5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos', 9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'];

    $totalFetched = 0;
    $created = 0;
    $updated = 0;
    $failed = 0;

    for ($year = $start; $year <= $end; $year++) {
        $items = $scraper->fetchYear($year);
        $totalFetched += count($items);
        if ($items === []) {
            $this->warn("{$year}: kayıt bulunamadı veya sayfa erişilemedi.");

            continue;
        }

        foreach ($items as $item) {
            $titleRaw = trim((string) ($item['title'] ?? ''));
            $fileUrl = trim((string) ($item['file_url'] ?? ''));
            if ($titleRaw === '' || $fileUrl === '') {
                $failed++;

                continue;
            }

            $title = Str::limit($titleRaw, 240, '');
            $titleLower = mb_strtolower($titleRaw);

            $monthNum = null;
            foreach ($monthMap as $token => $num) {
                if (str_contains($titleLower, $token)) {
                    $monthNum = $num;
                    break;
                }
            }

            $meetingDate = sprintf('%04d-%02d-01', $year, $monthNum ?: 1);
            $monthText = $monthNum ? ($monthLabelByNum[$monthNum] ?? 'Bilinmeyen') : 'Bilinmeyen';

            $field = 'decision_file';
            if (str_contains($titleLower, 'gündem') || str_contains($titleLower, 'gundem')) {
                $field = 'agenda_file';
            } elseif (
                str_contains($titleLower, 'komisyon')
                || str_contains($titleLower, 'rapor')
                || str_contains($titleLower, 'raporu')
                || str_contains($titleLower, 'raporları')
            ) {
                $field = 'commission_file';
            }

            $pdfPath = null;
            if (! $dryRun) {
                try {
                    $res = Http::retry(3, 400)
                        ->timeout(60)
                        ->withHeaders(['User-Agent' => 'KirklareliCouncilSync/1.0'])
                        ->get($fileUrl);
                    if (! $res->successful()) {
                        $failed++;

                        continue;
                    }
                    $contentType = mb_strtolower((string) $res->header('Content-Type', ''));
                    if (! str_contains($contentType, 'pdf') && ! str_contains(mb_strtolower($fileUrl), '.pdf')) {
                        $failed++;

                        continue;
                    }

                    $pdfPath = 'council-decisions/imported/'.$year.'/'.substr(sha1($fileUrl), 0, 24).'.pdf';
                    Storage::disk('public')->put($pdfPath, $res->body());
                } catch (\Throwable) {
                    $failed++;

                    continue;
                }
            }

            $groupTitle = $year.' '.$monthText.' Ayı Meclis Evrakları';
            $group = CouncilDecision::query()
                ->where('year', $year)
                ->where('month', $monthText)
                ->where('title', $groupTitle)
                ->first();

            if ($group && blank($group->{$field})) {
                if (! $dryRun) {
                    $group->{$field} = $pdfPath;
                    $group->meeting_date = $meetingDate;
                    $group->save();
                }
                $updated++;

                continue;
            }

            $existing = CouncilDecision::query()
                ->where('year', $year)
                ->where('title', $title)
                ->first();

            $payload = [
                'year' => $year,
                'month' => $monthText,
                'title' => $title,
                'meeting_date' => $meetingDate,
                'agenda_file' => $field === 'agenda_file' ? $pdfPath : null,
                'decision_file' => $field === 'decision_file' ? $pdfPath : null,
                'commission_file' => $field === 'commission_file' ? $pdfPath : null,
            ];

            if ($existing) {
                if (! $dryRun) {
                    $existing->fill($payload)->save();
                }
                $updated++;
            } else {
                if (! $dryRun) {
                    CouncilDecision::query()->create($payload);
                }
                $created++;
            }
        }

        $this->line("{$year}: işlendi (".count($items).' kayıt)');
    }

    $this->info("Yıl aralığı: {$start}-{$end}");
    $this->info("Toplam çekilen kayıt: {$totalFetched}");
    $this->info("Yeni kayıt: {$created}");
    $this->info("Güncellenen kayıt: {$updated}");
    $this->info("Atlanan/Hatalı: {$failed}");
    $this->info($dryRun ? 'Dry-run tamamlandı (DB/PDF yazılmadı).' : 'Meclis kararları ve PDF dosyaları aktarımı tamamlandı.');

    return self::SUCCESS;
})->purpose('2019-2025 kararlari sayfalarindan meclis PDFlerini indirip council_decisions tablosuna kaydeder');

Artisan::command('consolidate:meclis-kararlari {--dry-run}', function () {
    $dryRun = (bool) $this->option('dry-run');

    $rows = CouncilDecision::query()
        ->orderBy('year')
        ->orderBy('meeting_date')
        ->orderBy('id')
        ->get();

    $groups = [];
    foreach ($rows as $row) {
        $key = implode('|', [
            (string) ($row->year ?? 0),
            (string) ($row->month ?? 'Bilinmeyen'),
        ]);

        if (! isset($groups[$key])) {
            $groups[$key] = [];
        }
        $groups[$key][] = $row;
    }

    $mergedGroups = 0;
    $deletedRows = 0;

    foreach ($groups as $items) {
        if (count($items) <= 1) {
            continue;
        }

        /** @var CouncilDecision $base */
        $base = $items[0];
        $base->agenda_file = $base->agenda_file ?: null;
        $base->decision_file = $base->decision_file ?: null;
        $base->commission_file = $base->commission_file ?: null;

        for ($i = 1; $i < count($items); $i++) {
            /** @var CouncilDecision $current */
            $current = $items[$i];
            if (! $base->agenda_file && $current->agenda_file) {
                $base->agenda_file = $current->agenda_file;
            }
            if (! $base->decision_file && $current->decision_file) {
                $base->decision_file = $current->decision_file;
            }
            if (! $base->commission_file && $current->commission_file) {
                $base->commission_file = $current->commission_file;
            }
        }

        $base->title = ($base->year ?? '').' '.($base->month ?? 'Bilinmeyen').' Ayı Meclis Evrakları';

        if (! $dryRun) {
            $base->save();
        }

        for ($i = 1; $i < count($items); $i++) {
            if (! $dryRun) {
                $items[$i]->delete();
            }
            $deletedRows++;
        }

        $mergedGroups++;
    }

    $this->info('Konsolide edilen grup: '.$mergedGroups);
    $this->info('Silinen mükerrer kayıt: '.$deletedRows);
    $this->info($dryRun ? 'Dry-run tamamlandı (DB yazılmadı).' : 'Meclis kararları konsolidasyonu tamamlandı.');

    return self::SUCCESS;
})->purpose('Ayni ay/toplantiya ait meclis dosyalarini tek kayitta birlestirir');

Artisan::command('sync:meclis-uyeleri {--dry-run}', function () {
    $dryRun = (bool) $this->option('dry-run');

    $apiUrl = 'https://api.kirklarelibelediyesi.com/api/member';
    $authToken = 'Access-Token UnyO4DCBz6R5InNwvB3zXfvAN6SdmHpn';

    try {
        $response = Http::retry(3, 400)
            ->timeout(45)
            ->withHeaders([
                'Authorization' => $authToken,
                'Accept' => 'application/json',
                'User-Agent' => 'KirklareliCouncilMemberSync/1.0',
            ])
            ->get($apiUrl);
    } catch (\Throwable $e) {
        $this->error('Kaynak API erişilemedi: '.$e->getMessage());

        return self::FAILURE;
    }

    if (! $response->successful()) {
        $this->error('Kaynak API başarısız: HTTP '.$response->status());

        return self::FAILURE;
    }

    $payload = $response->json();
    $items = is_array($payload['data'] ?? null) ? $payload['data'] : [];
    if ($items === []) {
        $this->warn('Kaynak API boş veri döndürdü.');

        return self::SUCCESS;
    }

    $created = 0;
    $updated = 0;
    $skipped = 0;
    $activeNames = [];
    $order = 0;

    foreach ($items as $item) {
        if (! is_array($item)) {
            $skipped++;

            continue;
        }

        $status = (string) ($item['status'] ?? '');
        if (mb_strtolower($status) !== 'active') {
            continue;
        }

        $name = trim((string) ($item['title'] ?? ''));
        $partyName = trim((string) ($item['party'] ?? ''));
        $imageUrl = trim((string) ($item['imageUrl'] ?? ''));
        if ($name === '') {
            $skipped++;

            continue;
        }
        $activeNames[] = $name;
        $order++;

        $politicalPartyId = null;
        if ($partyName !== '') {
            if (! $dryRun) {
                $party = PoliticalParty::query()->firstOrCreate(
                    ['name' => Str::limit($partyName, 255, '')]
                );
                $politicalPartyId = $party->id;
            }
        }

        $imagePath = null;
        if ($imageUrl !== '') {
            if (! $dryRun) {
                try {
                    $imgResponse = Http::retry(2, 300)->timeout(30)->get($imageUrl);
                    if ($imgResponse->successful()) {
                        $contentType = mb_strtolower((string) $imgResponse->header('Content-Type', ''));
                        $ext = 'jpg';
                        if (str_contains($contentType, 'png')) {
                            $ext = 'png';
                        } elseif (str_contains($contentType, 'webp')) {
                            $ext = 'webp';
                        } elseif (str_contains($contentType, 'gif')) {
                            $ext = 'gif';
                        }

                        $imagePath = 'council-members/imported/'.substr(sha1($name.$imageUrl), 0, 20).'.'.$ext;
                        Storage::disk('public')->put($imagePath, $imgResponse->body());
                    }
                } catch (\Throwable) {
                    // Görsel alınamasa da kaydı oluşturmaya devam et.
                    $imagePath = null;
                }
            }
        }

        $member = CouncilMember::query()->where('name', $name)->first();
        if ($member) {
            $updated++;
            if (! $dryRun) {
                $member->fill([
                    'title' => 'Meclis Üyesi',
                    'party' => $partyName !== '' ? Str::limit($partyName, 255, '') : null,
                    'political_party_id' => $politicalPartyId,
                    'image_path' => $imagePath ?: $member->image_path,
                    'order' => $order,
                    'is_active' => true,
                ])->save();
            }
        } else {
            $created++;
            if (! $dryRun) {
                CouncilMember::query()->create([
                    'name' => Str::limit($name, 255, ''),
                    'title' => 'Meclis Üyesi',
                    'party' => $partyName !== '' ? Str::limit($partyName, 255, '') : null,
                    'political_party_id' => $politicalPartyId,
                    'image_path' => $imagePath,
                    'order' => $order,
                    'is_active' => true,
                ]);
            }
        }
    }

    if (! $dryRun && $activeNames !== []) {
        CouncilMember::query()
            ->whereNotIn('name', $activeNames)
            ->update(['is_active' => false]);
    }

    $this->info('Kaynak: '.$apiUrl);
    $this->info('Aktif üye sayısı (kaynak): '.count($activeNames));
    $this->info('Yeni: '.$created);
    $this->info('Güncellenen: '.$updated);
    $this->info('Atlanan: '.$skipped);
    $this->info($dryRun ? 'Dry-run tamamlandı (DB yazılmadı).' : 'Meclis üyeleri aktarımı tamamlandı.');

    return self::SUCCESS;
})->purpose('Belediye meclis üyelerini kaynak APIden council_members tablosuna aktarır');

Artisan::command('sync:organizasyon-semasi {--dry-run}', function () {
    $this->error(
        'Bu komut devre dışı: önceki kaynak (mudurluklerimiz yan menüsü) gerçek müdürlük listesi değildi; '
        .'ulaşım ve öğrenci hizmetleri alt sayfalarını müdürlük sanıyordu. Mali Hizmetler Müdürlüğü gibi kayıtlar '
        .'Filament veya doğru kaynakla elle eklenmeli; bu komut veritabanına yazmaz.'
    );

    return self::FAILURE;
})->purpose('Eski organizasyon şeması senkronu (kapatıldı; yanlış kaynak)');

Artisan::command(
    'belsis:probe-wsdl {url : WSDL veya servis URL’si} {--insecure : TLS sertifika doğrulamasını kapat (yalnızca teşhis)} {--soap : SoapClient ile WSDL yüklemeyi dene}',
    function (string $url): int {
        $insecure = (bool) $this->option('insecure');
        $trySoap = (bool) $this->option('soap');

        $ssl = $insecure
            ? ['verify_peer' => false, 'verify_peer_name' => false]
            : ['verify_peer' => true, 'verify_peer_name' => true];

        $context = stream_context_create([
            'http' => [
                'timeout' => 25,
                'follow_location' => 1,
                'max_redirects' => 10,
                'ignore_errors' => true,
            ],
            'ssl' => $ssl,
        ]);

        $this->info('URL: '.$url);
        $this->info('TLS doğrulama: '.($insecure ? 'kapalı (teşhis)' : 'açık'));

        $headers = @get_headers($url, true, $context) ?: false;
        if ($headers === false) {
            $this->error('get_headers başarısız (ağ / TLS / DNS).');

            return self::FAILURE;
        }

        if (is_array($headers) && isset($headers[0])) {
            $statusLines = is_array($headers[0]) ? $headers[0] : [$headers[0]];
            foreach ($statusLines as $line) {
                $this->line('HTTP: '.trim((string) $line));
            }
        }

        $body = @file_get_contents($url, false, $context);
        if ($body === false) {
            $this->error('Gövde okunamadı.');

            return self::FAILURE;
        }

        $snippet = mb_substr(trim($body), 0, 400);
        $looksXml = str_starts_with(ltrim($body), '<') && (
            str_contains($snippet, 'wsdl:') ||
            str_contains($snippet, 'definitions') ||
            str_contains($snippet, 'WSDL') ||
            str_contains(mb_strtolower($snippet), 'schema')
        );
        $looksHtml = str_contains(mb_strtolower($snippet), '<!doctype html') || str_contains(mb_strtolower($snippet), '<html');

        $this->line('İlk 400 bayt (kırpılmış):');
        $this->line($snippet);
        $this->newLine();
        if ($looksXml) {
            $this->info('Tahmin: WSDL/XML benzeri içerik.');
        } elseif ($looksHtml) {
            $this->warn('Tahmin: HTML (WSDL değil; yanlış sanal sunucu veya servis kaldırılmış olabilir).');
        } else {
            $this->comment('İçerik türü net değil; manuel kontrol edin.');
        }

        if ($trySoap && class_exists(\SoapClient::class)) {
            $this->newLine();
            $this->info('SoapClient denemesi...');
            $opts = [
                'trace' => true,
                'exceptions' => true,
                'connection_timeout' => 25,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'encoding' => 'UTF-8',
            ];
            if ($insecure) {
                $opts['stream_context'] = stream_context_create([
                    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
                ]);
            }
            try {
                $client = new \SoapClient($url, $opts);
                $funcs = $client->__getFunctions();
                $this->info('SoapClient yüklendi; işlem sayısı: '.count($funcs));
                foreach (array_slice($funcs, 0, 25) as $sig) {
                    $this->line('  '.$sig);
                }
                if (count($funcs) > 25) {
                    $this->comment('  ... ve '.(count($funcs) - 25).' işlem daha');
                }
            } catch (\Throwable $e) {
                $this->error('SoapClient: '.$e->getMessage());

                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }
)->purpose('BELSIS (veya diğer) WSDL URL’sinin erişilebilirliğini ve içerik türünü teşhis eder');

Artisan::command(
    'belsis:fetch-wsdl {url : WSDL kaynak URL’si} {--output=belsis/tahakkuk.wsdl : storage/app altında hedef (göreli)} {--insecure : TLS doğrulamasını kapat}',
    function (string $url): int {
        $insecure = (bool) $this->option('insecure');
        $relative = ltrim(str_replace('\\', '/', (string) $this->option('output')), '/');
        $targetPath = storage_path('app/'.$relative);
        $dir = dirname($targetPath);
        if (! is_dir($dir)) {
            if (! @mkdir($dir, 0755, true) && ! is_dir($dir)) {
                $this->error('Klasör oluşturulamadı: '.$dir);

                return self::FAILURE;
            }
        }

        $this->info('Kaynak: '.$url);
        $this->info('Hedef: '.$targetPath);

        try {
            $response = Http::timeout(30)->withOptions(['verify' => ! $insecure])->get($url);
        } catch (\Throwable $e) {
            $this->error('İndirme hatası: '.$e->getMessage());

            return self::FAILURE;
        }

        if (! $response->successful()) {
            $this->error('HTTP '.$response->status());

            return self::FAILURE;
        }

        $body = $response->body();
        $lower = mb_strtolower($body);
        $looksWsdl = str_contains($lower, 'definitions') && (str_contains($lower, 'wsdl:') || str_contains($lower, 'targetnamespace') || str_contains($lower, '<types'));
        $looksHtml = str_contains($lower, '<!doctype html') || str_contains($lower, '<html');

        if ($looksHtml || ! $looksWsdl) {
            $this->warn('Yanıt WSDL gibi görünmüyor (HTML veya hata sayfası olabilir). Dosya yazılmadı.');
            $this->line(mb_substr($body, 0, 500));

            return self::FAILURE;
        }

        if (file_put_contents($targetPath, $body) === false) {
            $this->error('Dosya yazılamadı.');

            return self::FAILURE;
        }

        $this->info('WSDL kaydedildi. .env örneği:');
        $this->line('E_ODEME_WSDL_LOCAL='.$relative);
        $this->line('E_ODEME_SOAP_VERIFY_SSL='.($insecure ? 'false' : 'true'));

        return self::SUCCESS;
    }
)->purpose('BELSIS WSDL’yi indirip storage/app altına yazar (yalnızca gerçek WSDL XML ise)');
