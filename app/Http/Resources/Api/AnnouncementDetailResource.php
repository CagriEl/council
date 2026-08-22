<?php

namespace App\Http\Resources\Api;

use App\Support\Api\StorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Announcement */
class AnnouncementDetailResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'type_label' => AnnouncementListResource::typeLabel($this->type),
            'content_html' => $this->content,
            'image_url' => StorageUrl::fromPath($this->image_path),
            'file_url' => StorageUrl::fromPath($this->file_path),
            'gallery' => $this->whenLoaded('galleryImages', fn () => $this->galleryImages->map(fn ($image) => [
                'id' => $image->id,
                'image_url' => StorageUrl::fromPath($image->image_path),
                'sort_order' => $image->sort_order,
            ])->values()->all(), []),
            'date' => $this->date?->toIso8601String(),
            'published_at' => $this->published_at?->toIso8601String(),
            'unpublished_at' => $this->unpublished_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
