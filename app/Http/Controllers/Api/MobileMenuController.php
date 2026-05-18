<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileMenuController extends Controller
{
    /**
     * Header veya footer menü ağacı. `page_slug` dolu ise uygulama `/api/pages/{slug}` ile içerik çekebilir.
     *
     * Query: location — `header` veya `footer` (zorunlu)
     */
    public function index(Request $request): JsonResponse
    {
        $location = $request->query('location');
        if (! in_array($location, ['header', 'footer'], true)) {
            return response()->json([
                'message' => 'Geçerli bir location gerekli: header veya footer.',
            ], 422);
        }

        $menus = Menu::query()
            ->where('location', $location)
            ->where('is_active', true)
            ->with('page')
            ->orderBy('order')
            ->get();

        $byParent = $menus->groupBy(fn (Menu $m) => $m->parent_id ?? 0);

        $build = function (int $parentId) use (&$build, $byParent): array {
            $items = $byParent->get($parentId, collect());

            return $items->map(function (Menu $item) use (&$build) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'url' => $item->url,
                    'page_slug' => $item->page?->slug,
                    'children' => $build($item->id),
                ];
            })->values()->all();
        };

        return response()->json([
            'location' => $location,
            'items' => $build(0),
        ]);
    }
}
