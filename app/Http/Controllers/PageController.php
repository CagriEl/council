<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Slider;
use App\Models\QuickLink;
use App\Models\Announcement;
use App\Models\Mayor;
use App\Models\CouncilMember;
use App\Models\VicePresident;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        // Ana Sayfa Verileri
        $sliders = Slider::where('is_active', true)->orderBy('order')->get();
        $quickLinks = QuickLink::orderBy('order')->get();
        $headlines = News::where('is_headline', true)->latest()->take(5)->get(); // Slider haberleri
        $news = News::where('is_headline', false)->latest()->take(3)->get(); // Yan liste
        
        // 3 Sütun Duyurular
        $generalAnnouncements = Announcement::where('type', 'duyuru')->latest()->take(5)->get();
        $officialAnnouncements = Announcement::where('type', 'resmi')->latest()->take(5)->get();
        $tenders = Announcement::where('type', 'ihale')->latest()->take(5)->get();

        return view('home', compact('sliders', 'quickLinks', 'headlines', 'news', 'generalAnnouncements', 'officialAnnouncements', 'tenders'));
    }

    public function mudurlukDetay($slug)
{
    // Slug'a göre müdürlüğü bul, yoksa 404 hatası ver
    $mudurluk = \App\Models\Directorate::where('slug', $slug)->firstOrFail();

    return view('pages.mudurluk-detay', compact('mudurluk'));
}

public function mudurlukler()
{
    // Başkan Yardımcılarını, bağlı müdürlükleriyle beraber çek (Sırasına göre)
    $vicePresidents = \App\Models\VicePresident::with('directorates')->orderBy('order')->get();

    // Tüm müdürlükleri çek (Grid listeleme için)
    $allDirectorates = \App\Models\Directorate::with('vicePresident')->orderBy('name')->get();

    return view('pages.mudurlukler', compact('vicePresidents', 'allDirectorates'));
}
    public function meclis()
    {
        $mayor = Mayor::first();
        $members = CouncilMember::with('politicalParty')->orderBy('order')->get();
        return view('pages.meclis', compact('mayor', 'members'));
    }
    
    // Diğer sayfalar (mudurler, baskan vb.) benzer şekilde eklenecek...
}