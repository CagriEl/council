<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\VicePresident */
class OrganisationTreeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'order' => (int) ($this->order ?? 0),
            'directorates' => DirectorateListResource::collection(
                $this->relationLoaded('directorates') ? $this->directorates : collect()
            ),
        ];
    }
}
