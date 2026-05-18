<?php

namespace App\Http\Resources\Api;

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
            'image_url' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'file_url' => $this->file_path ? asset('storage/'.$this->file_path) : null,
            'date' => $this->date?->toIso8601String(),
            'published_at' => $this->published_at?->toIso8601String(),
            'unpublished_at' => $this->unpublished_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
