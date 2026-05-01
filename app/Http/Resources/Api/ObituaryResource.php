<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Obituary */
class ObituaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $burialPlace = $this->burial_place_type === 'city_cemetery'
            ? 'Şehir Mezarlığı'
            : ($this->burial_place_other ?? 'Diğer');

        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'death_date' => optional($this->death_date)->format('Y-m-d'),
            'prayer_time' => $this->formatPrayerTime(),
            'mosque' => $this->mosque,
            'burial_place' => $burialPlace,
            'burial_place_type' => $this->burial_place_type,
            'burial_place_other' => $this->burial_place_other,
        ];
    }

    private function formatPrayerTime(): ?string
    {
        if (blank($this->prayer_time)) {
            return null;
        }

        if (is_string($this->prayer_time) && str_contains($this->prayer_time, ':')) {
            return substr($this->prayer_time, 0, 5);
        }

        return null;
    }
}
