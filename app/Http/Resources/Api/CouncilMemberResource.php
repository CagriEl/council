<?php

namespace App\Http\Resources\Api;

use App\Support\Api\StorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\CouncilMember */
class CouncilMemberResource extends JsonResource
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
            'party' => $this->party,
            'image_url' => StorageUrl::fromPath($this->image_path),
            'sort_order' => (int) ($this->order ?? 0),
            'political_party' => $this->when(
                $this->relationLoaded('politicalParty') && $this->politicalParty,
                fn () => [
                    'id' => $this->politicalParty->id,
                    'name' => $this->politicalParty->name,
                    'short_name' => $this->politicalParty->short_name,
                    'logo_url' => StorageUrl::fromPath($this->politicalParty->logo_path),
                    'color' => $this->politicalParty->color,
                ]
            ),
        ];
    }
}
