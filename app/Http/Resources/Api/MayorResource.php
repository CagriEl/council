<?php

namespace App\Http\Resources\Api;

use App\Support\Api\StorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Mayor */
class MayorResource extends JsonResource
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
            'image_url' => StorageUrl::fromPath($this->image_path),
            'description_html' => $this->description,
            'message_html' => $this->message,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
