<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Directorate */
class DirectorateListResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'vice_president' => $this->when(
                $this->relationLoaded('vicePresident') && $this->vicePresident,
                fn () => [
                    'id' => $this->vicePresident->id,
                    'name' => $this->vicePresident->name,
                    'title' => $this->vicePresident->title,
                ]
            ),
        ];
    }
}
