<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\News;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [];

        $static = [
            ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => url('/baskan'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('meclis'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('mudurler'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('announcements.index'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('iletisim'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('rehber'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('meclis-kararlari'), 'changefreq' => 'weekly', 'priority' => '0.6'],
            ['loc' => route('activity-reports.index'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('strategic-plans.index'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('transparency.show'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('search'), 'changefreq' => 'weekly', 'priority' => '0.4'],
        ];

        foreach ($static as $item) {
            $urls[] = $item + ['lastmod' => now()->toAtomString()];
        }

        Page::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['slug', 'updated_at'])
            ->each(function (Page $page) use (&$urls) {
                $urls[] = [
                    'loc' => route('page.detail', $page->slug),
                    'lastmod' => optional($page->updated_at)->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            });

        News::query()
            ->publishedForPublic()
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at', 'published_at'])
            ->each(function (News $news) use (&$urls) {
                $urls[] = [
                    'loc' => route('news.detail', $news->slug),
                    'lastmod' => optional($news->updated_at ?? $news->published_at)->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            });

        Announcement::query()
            ->publishedForPublic()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(2000)
            ->get(['slug', 'updated_at', 'date'])
            ->each(function (Announcement $announcement) use (&$urls) {
                $urls[] = [
                    'loc' => route('announcement.show', $announcement->slug),
                    'lastmod' => optional($announcement->updated_at ?? $announcement->date)->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            });

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
