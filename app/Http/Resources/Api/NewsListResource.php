<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\News */
class NewsListResource extends JsonResource
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
            'summary' => $this->summary,
            'category' => $this->category,
            'category_label' => self::categoryLabel($this->category),
            'image_url' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'published_at' => $this->published_at?->toIso8601String(),
            'unpublished_at' => $this->unpublished_at?->toIso8601String(),
            'is_headline' => (bool) $this->is_headline,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    public static function categoryLabel(?string $category): string
    {
        return match ($category) {
            'belediye' => 'Belediye',
            'kultur' => 'Kültür ve Sanat',
            'spor' => 'Spor',
            'cevre' => 'Çevre ve Kent',
            'sosyal' => 'Sosyal Hizmetler',
            default => $category ?? '',
        };
    }
}
