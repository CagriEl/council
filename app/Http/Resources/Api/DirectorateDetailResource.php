<?php

namespace App\Http\Resources\Api;

use App\Support\Api\StorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Directorate */
class DirectorateDetailResource extends JsonResource
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
            'manager_name' => $this->manager_name,
            'manager_title' => $this->manager_title,
            'manager_image_url' => StorageUrl::fromPath($this->manager_image),
            'phone' => $this->phone,
            'email' => $this->email,
            'description_html' => $this->description,
            'vice_president' => $this->when(
                $this->relationLoaded('vicePresident') && $this->vicePresident,
                fn () => [
                    'id' => $this->vicePresident->id,
                    'name' => $this->vicePresident->name,
                    'title' => $this->vicePresident->title,
                    'image_url' => StorageUrl::fromPath($this->vicePresident->image_path),
                ]
            ),
            'latest_announcements' => AnnouncementListResource::collection(
                $this->relationLoaded('latestAnnouncements') ? $this->latestAnnouncements : collect()
            ),
        ];
    }
}
