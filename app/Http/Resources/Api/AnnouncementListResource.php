<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin \App\Models\Announcement */
class AnnouncementListResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $plain = Str::limit(strip_tags((string) $this->content), 220);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'type_label' => self::typeLabel($this->type),
            'excerpt' => $plain,
            'image_url' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'date' => $this->date?->toIso8601String(),
            'published_at' => $this->published_at?->toIso8601String(),
            'unpublished_at' => $this->unpublished_at?->toIso8601String(),
            'has_attachment' => filled($this->file_path),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    public static function typeLabel(?string $type): string
    {
        return match ($type) {
            'duyuru' => 'Genel duyuru',
            'resmi' => 'Resmî ilan',
            'ihale' => 'İhale duyurusu',
            default => $type ?? '',
        };
    }
}
