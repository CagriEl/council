<?php

namespace App\Http\Resources\Api;

use App\Support\Api\StorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\CouncilDecision */
class CouncilDecisionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'year' => (int) $this->year,
            'month' => $this->month,
            'title' => $this->title,
            'meeting_date' => $this->meeting_date?->toIso8601String(),
            'agenda_file_url' => StorageUrl::fromPath($this->agenda_file),
            'decision_file_url' => StorageUrl::fromPath($this->decision_file),
            'commission_file_url' => StorageUrl::fromPath($this->commission_file),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
