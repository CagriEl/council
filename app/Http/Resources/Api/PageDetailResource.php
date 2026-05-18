<?php

namespace App\Http\Resources\Api;

use App\Support\Api\StorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Page */
class PageDetailResource extends JsonResource
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
            'content_html' => $this->content,
            'image_url' => StorageUrl::fromPath($this->image_path),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
