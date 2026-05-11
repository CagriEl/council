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

        // 3. Ana sayfa «KIRKLARELİ'DEN HABERLER»: son 5 genel duyuru (type=duyuru) + alttaki sütun için devamı
        $duyuruAnaSayfa = Announcement::publishedForPublic()
            ->where('type', 'duyuru')
            ->latest('date')
            ->take(10)
            ->get();

        $kirklareliFromDuyurular = $duyuruAnaSayfa->take(5);
        $generalAnnouncements = $duyuruAnaSayfa->slice(5)->values();

        // 4. Sol küçük slider (hero): «Kırklareli'den Haberler» ile aynı kaynaktan son 3 genel duyuru
        $kirklareliHeroMini = $kirklareliFromDuyurular->take(3)->values();

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
            'kirklareliFromDuyurular',
            'kirklareliHeroMini',
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
        $members = CouncilMember::with('politicalParty')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('pages.meclis', compact('mayor', 'members'));
    }

    /**
     * MÜDÜRLÜKLER (ORGANİZASYON ŞEMASI) SAYFASI
     */
    public function mudurler()
    {
        $mayor = Mayor::query()->where('is_active', true)->first()
            ?? Mayor::query()->first();

        $mayorDirectorates = Directorate::query()
            ->whereNull('vice_president_id')
            ->orderBy('name')
            ->get();

        $vicePresidents = VicePresident::query()
            ->with(['directorates' => fn ($q) => $q->orderBy('name')])
            ->orderBy('order')
            ->get();

        return view('pages.mudurler', compact('mayor', 'mayorDirectorates', 'vicePresidents'));
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
