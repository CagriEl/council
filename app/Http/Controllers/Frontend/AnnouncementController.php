<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    private const TYPES = ['duyuru', 'resmi', 'ihale'];

    public function index(Request $request): View
    {
        $tip = $request->query('tip');
        $currentTip = in_array($tip, self::TYPES, true) ? $tip : null;

        $query = Announcement::query()
            ->publishedForPublic()
            ->when($currentTip, fn ($q) => $q->where('type', $currentTip))
            ->orderByDesc('date')
            ->orderByDesc('id');

        $announcements = $query->paginate(12)->withQueryString();

        return view('pages.duyurular', compact('announcements', 'currentTip'));
    }

    public function show(string $slug): View
    {
        $announcement = Announcement::query()
            ->publishedForPublic()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.announcement_detail', compact('announcement'));
    }
}
