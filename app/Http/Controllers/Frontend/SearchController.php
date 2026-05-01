<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CouncilDecision;
use App\Models\Directorate;
use App\Models\News;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $q = Str::limit(trim((string) $request->query('q', '')), 200);
        $news = collect();
        $announcements = collect();
        $pages = collect();
        $councilDecisions = collect();
        $directorates = collect();

        if (Str::length($q) >= 2) {
            $like = '%'.addcslashes($q, '%_\\').'%';

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

            $pages = Page::query()
                ->where('is_active', true)
                ->where(function ($query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('content', 'like', $like);
                })
                ->orderByDesc('updated_at')
                ->limit(30)
                ->get();

            $councilDecisions = CouncilDecision::query()
                ->where(function ($query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('year', 'like', $like)
                        ->orWhere('month', 'like', $like);
                })
                ->orderByDesc('meeting_date')
                ->orderByDesc('id')
                ->limit(30)
                ->get();

            $directorates = Directorate::query()
                ->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                        ->orWhere('manager_name', 'like', $like)
                        ->orWhere('description', 'like', $like);
                })
                ->orderBy('name')
                ->limit(30)
                ->get();
        }

        return view('pages.arama', [
            'q' => $q,
            'news' => $news,
            'announcements' => $announcements,
            'pages' => $pages,
            'councilDecisions' => $councilDecisions,
            'directorates' => $directorates,
        ]);
    }
}
