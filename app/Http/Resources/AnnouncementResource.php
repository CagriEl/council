<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Scraper çıktısı: resmî duyuru listesi öğesi.
 *
 * @property-read array{title: string, image_url: ?string, detail_url: string} $resource
 */
class AnnouncementResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->resource['title'] ?? '',
            'image_url' => $this->resource['image_url'] ?? null,
            'detail_url' => $this->resource['detail_url'] ?? '',
        ];
    }
}
