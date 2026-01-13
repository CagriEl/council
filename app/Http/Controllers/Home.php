<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Models\QuickLink;
use App\Models\News;
use App\Models\Announcement;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        // Ayarları key-value dizisine çevir (Örn: $settings['phone'])
        $settings = Setting::pluck('value', 'key')->toArray();

        // Ana Manşet Slider
        $sliders = Slider::where('is_active', true)->orderBy('date', 'desc')->take(5)->get();

        // Kare Butonlar
        $quickLinks = QuickLink::orderBy('sort_order')->get();

        // Yan Liste Haberler
        $sideNews = News::orderBy('date', 'desc')->take(3)->get();

        // Duyurular (3 Sütun)
        $generalAnnouncements = Announcement::where('type', 'general')->latest()->take(5)->get();
        $officialAnnouncements = Announcement::where('type', 'official')->latest()->take(5)->get();
        $tenderAnnouncements = Announcement::where('type', 'tender')->latest()->take(5)->get();

        return view('home', compact(
            'settings', 
            'sliders', 
            'quickLinks', 
            'sideNews', 
            'generalAnnouncements', 
            'officialAnnouncements', 
            'tenderAnnouncements'
        ));
    }
}