<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        // Duyuru listeleme sayfası (Burası muhtemelen sizde var)
        $announcements = Announcement::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
return view('pages.announcement_detail', compact('announcement'));    }

    public function show($slug)
    {
        // Slug'a göre duyuruyu bul, bulamazsa 404 ver
        $announcement = Announcement::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

return view('pages.announcement_detail', compact('announcement'));    }
}