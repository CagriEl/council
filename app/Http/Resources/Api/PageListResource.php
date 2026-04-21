<?php

namespace App\Http\Resources\Api;

use App\Support\Api\StorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Page */
class PageListResource extends JsonResource
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
            'image_url' => StorageUrl::fromPath($this->image_path),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
