<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\CouncilDecision;
use App\Models\CouncilMember;
use App\Models\Directorate;
use App\Models\Mayor;
use App\Models\News;
use App\Models\QuickLink;
use App\Models\Slider;
use App\Models\VicePresident;

class PageController extends Controller
{
    /**
     * ANA SAYFA
     */
    public function home()
    {
        // 1. Büyük Slider (Manşet) - Aktif olanlar
        $sliders = Slider::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        // 2. Kare Butonlar (Hızlı Linkler)
        $quickLinks = QuickLink::orderBy('order', 'asc')->get();

        // 3. KIRKLARELİ'DEN HABERLER bloğu için son 5 genel duyuru
        $latestDuyurular = Announcement::publishedForPublic()
            ->where('type', 'duyuru')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // 5. Sol küçük slider için son 3 genel duyuru
        $heroNews = Announcement::publishedForPublic()
            ->where('type', 'duyuru')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        // 6. Duyurular (3 Sütun)
        // Veritabanındaki 'type' alanlarına göre (duyuru, resmi, ihale)
        $generalAnnouncements = Announcement::publishedForPublic()
            ->where('type', 'duyuru')
            ->latest('date')
            ->take(5)
            ->get();

        $officialAds = Announcement::publishedForPublic()
            ->where('type', 'resmi')
            ->latest('date')
            ->take(5)
            ->get();

        $tenders = Announcement::publishedForPublic()
            ->where('type', 'ihale')
            ->latest('date')
            ->take(5)
            ->get();

        // 7. Başkan Bilgisi
        $mayor = Mayor::first();

        return view('home', compact(
            'sliders',
            'quickLinks',
            'latestDuyurular',
            'heroNews', // <-- EKLENDİ: Artık view dosyasında hata vermeyecek
            'generalAnnouncements',
            'officialAds',
            'tenders',
            'mayor'
        ));
    }

    /**
     * BAŞKAN SAYFASI
     */
    public function baskan()
    {
        $mayor = Mayor::first();

        return view('pages.baskan', compact('mayor'));
    }

    /**
     * MECLİS ÜYELERİ SAYFASI
     */
    public function meclis()
    {
        $mayor = Mayor::first();
        $members = CouncilMember::with('politicalParty')->orderBy('order')->get();

        return view('pages.meclis', compact('mayor', 'members'));
    }

    /**
     * MÜDÜRLÜKLER (ORGANİZASYON ŞEMASI) SAYFASI
     */
    public function mudurler()
    {
        $vicePresidents = VicePresident::with(['directorates' => fn ($q) => $q->orderBy('name')])
            ->orderBy('order')
            ->get();

        $allDirectorates = Directorate::with('vicePresident')->orderBy('name')->get();

        // Başkana doğrudan bağlı birimler (şemada başkanın yanında)
        $mayorAttached = Directorate::query()
            ->whereNull('vice_president_id')
            ->orderBy('name')
            ->get();

        $mayorDirectorates = collect([
            [
                'label' => 'Teftiş Kurulu Müdürlüğü',
                'match' => ['teftiş', 'teftis'],
            ],
            [
                'label' => 'İç Denetim',
                'match' => ['iç denetim', 'ic denetim'],
            ],
            [
                'label' => 'Özel Kalem Müdürlüğü',
                'match' => ['özel kalem', 'ozel kalem'],
            ],
        ])->map(function (array $item) use ($mayorAttached, $allDirectorates) {
            $found = $mayorAttached->first(function ($d) use ($item) {
                $name = mb_strtolower($d->name);
                foreach ($item['match'] as $needle) {
                    if (str_contains($name, mb_strtolower($needle))) {
                        return true;
                    }
                }

                return false;
            });

            // VP altında yanlışlıkla kayıtlıysa yine de bul
            if (! $found) {
                $found = $allDirectorates->first(function ($d) use ($item) {
                    $name = mb_strtolower($d->name);
                    foreach ($item['match'] as $needle) {
                        if (str_contains($name, mb_strtolower($needle))) {
                            return true;
                        }
                    }

                    return false;
                });
            }

            return (object) [
                'name' => $item['label'],
                'slug' => $found->slug ?? null,
                'manager_name' => $found->manager_name ?? null,
            ];
        });

        return view('pages.mudurler', compact('vicePresidents', 'allDirectorates', 'mayorDirectorates'));
    }

    /**
     * MÜDÜRLÜK DETAY SAYFASI
     */
    public function mudurlukDetay($slug)
    {
        $mudurluk = Directorate::where('slug', $slug)->firstOrFail();
        $latestAnnouncements = Announcement::publishedForPublic()->latest('date')->take(5)->get();

        return view('pages.mudurluk-detay', compact('mudurluk', 'latestAnnouncements'));
    }

    /**
     * MECLİS KARARLARI SAYFASI
     */
    public function meclisKararlari()
    {
        $years = CouncilDecision::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $decisions = CouncilDecision::orderBy('meeting_date', 'desc')->get();

        return view('pages.meclis-kararlari', compact('years', 'decisions'));
    }

    /**
     * HABER DETAY SAYFASI
     */
    public function haberDetay($slug)
    {
        $news = News::publishedForPublic()->where('slug', $slug)->firstOrFail();
        $otherNews = News::publishedForPublic()
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('pages.haber-detay', compact('news', 'otherNews'));
    }

    /**
     * DUYURU DETAY SAYFASI (Eğer gerekirse)
     */
    public function duyuruDetay($slug)
    {
        $announcement = Announcement::publishedForPublic()->where('slug', $slug)->firstOrFail();

        return view('pages.duyuru-detay', compact('announcement'));
    }
}
