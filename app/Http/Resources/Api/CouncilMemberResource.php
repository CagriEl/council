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
        $displayParty = $this->displayPartyName();
        $relation = $this->relationLoaded('politicalParty') ? $this->politicalParty : null;

        $partyPayload = null;
        if ($relation) {
            $isIndependent = $displayParty === 'Bağımsız'
                || str_contains(mb_strtolower((string) $relation->name), 'bağımsız')
                || str_contains(mb_strtolower((string) $relation->name), 'diğer');

            $partyPayload = [
                'id' => $relation->id,
                'name' => $displayParty ?: $relation->name,
                'short_name' => $relation->short_name,
                'logo_url' => StorageUrl::fromPath($relation->logo_path),
                'color' => $isIndependent ? '#64748B' : $relation->color,
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'party' => $displayParty,
            'image_url' => StorageUrl::fromPath($this->image_path),
            'sort_order' => (int) ($this->order ?? 0),
            'political_party' => $this->when($partyPayload !== null, $partyPayload),
        ];
    }
}
