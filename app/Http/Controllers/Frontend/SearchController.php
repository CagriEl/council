<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $q = Str::limit(trim((string) $request->query('q', '')), 200);
        $news = collect();
        $announcements = collect();

        if (Str::length($q) >= 2) {
            $like = '%'.addcslashes($q, '%_\\').'%';

            try {
                $news = News::query()
                    ->publishedForPublic()
                    ->where(function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('summary', 'like', $like)
                            ->orWhere('content', 'like', $like);
                    })
                    ->orderByDesc('published_at')
                    ->orderByDesc('id')
                    ->limit(50)
                    ->get();
            } catch (Throwable $e) {
                Log::warning('Search news query failed', ['message' => $e->getMessage()]);
                $news = collect();
            }

            try {
                $announcements = Announcement::query()
                    ->publishedForPublic()
                    ->where(function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('content', 'like', $like);
                    })
                    ->orderByDesc('date')
                    ->orderByDesc('id')
                    ->limit(50)
                    ->get();
            } catch (Throwable $e) {
                Log::warning('Search announcements query failed', ['message' => $e->getMessage()]);
                $announcements = collect();
            }
        }

        return view('pages.arama', [
            'q' => $q,
            'news' => $news,
            'announcements' => $announcements,
        ]);
    }
}
