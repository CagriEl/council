<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    private const TYPES = ['duyuru', 'resmi', 'ihale'];

    public function index(Request $request): View
    {
        $tip = $request->query('tip');
        $currentTip = in_array($tip, self::TYPES, true) ? $tip : null;
        $search = Str::limit(trim((string) $request->query('q', '')), 200);

        $query = Announcement::query()
            ->publishedForPublic()
            ->when($currentTip, fn ($q) => $q->where('type', $currentTip))
            ->when(Str::length($search) >= 2, function ($q) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';

                $q->where(function ($inner) use ($like) {
                    $inner->where('title', 'like', $like)
                        ->orWhere('content', 'like', $like);
                });
            })
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $announcements = $query->paginate(12)->withQueryString();

        return view('pages.duyurular', compact('announcements', 'currentTip', 'search'));
    }

    public function show(string $slug): View
    {
        $announcement = Announcement::query()
            ->publishedForPublic()
            ->with('galleryImages')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.announcement_detail', compact('announcement'));
    }
}
