<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Slider;
use App\Models\QuickLink;
use App\Models\Announcement;
use App\Models\Mayor;
use App\Models\CouncilMember;
use App\Models\VicePresident;
use App\Models\Directorate;
use App\Models\CouncilDecision;
use Illuminate\Http\Request;

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

        // 3. Manşet Haberler (Büyük Slider İçin)
        // 'is_headline' sütunu true olan son 5 haber
        $headlines = News::where('is_headline', true)
                        ->latest('published_at')
                        ->take(5)
                        ->get();

        // 4. Yan Liste Haberleri (Slider Yanı)
        // 'is_headline' sütunu false olan son 3 haber
        $sideNews = News::where('is_headline', false)
                        ->latest('published_at')
                        ->take(3)
                        ->get();

        // 5. HATA ÇÖZÜMÜ: Sol Küçük Slider Haberleri ($heroNews)
        // En son eklenen 5 haberi burası için çekiyoruz
        $heroNews = News::latest('published_at')->take(5)->get();

        // 6. Duyurular (3 Sütun)
        // Veritabanındaki 'type' alanlarına göre (duyuru, resmi, ihale)
        $generalAnnouncements = Announcement::where('type', 'duyuru')
                                    ->latest('date')
                                    ->take(5)
                                    ->get();

        $officialAds = Announcement::where('type', 'resmi')
                                    ->latest('date')
                                    ->take(5)
                                    ->get();

        $tenders = Announcement::where('type', 'ihale')
                        ->latest('date')
                        ->take(5)
                        ->get();

        // 7. Başkan Bilgisi
        $mayor = Mayor::first();

        return view('home', compact(
            'sliders',
            'quickLinks',
            'headlines',
            'sideNews',
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
        $vicePresidents = VicePresident::with('directorates')->orderBy('order')->get();
        $allDirectorates = Directorate::with('vicePresident')->orderBy('name')->get();

        return view('pages.mudurler', compact('vicePresidents', 'allDirectorates'));
    }

    /**
     * MÜDÜRLÜK DETAY SAYFASI
     */
    public function mudurlukDetay($slug)
    {
        $mudurluk = Directorate::where('slug', $slug)->firstOrFail();
        $latestAnnouncements = Announcement::latest('date')->take(5)->get();

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
        $news = News::where('slug', $slug)->firstOrFail();
        $otherNews = News::where('id', '!=', $news->id)->latest('published_at')->take(5)->get();

        return view('pages.haber-detay', compact('news', 'otherNews'));
    }

    /**
     * DUYURU DETAY SAYFASI (Eğer gerekirse)
     */
    public function duyuruDetay($slug)
    {
        $announcement = Announcement::where('slug', $slug)->firstOrFail();
        return view('pages.duyuru-detay', compact('announcement'));
    }
}