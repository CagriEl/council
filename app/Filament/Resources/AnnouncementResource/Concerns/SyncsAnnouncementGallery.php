<?php

namespace App\Filament\Resources\AnnouncementResource\Concerns;

trait SyncsAnnouncementGallery
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record?->exists) {
            $this->record->loadMissing('galleryImages');
            $data['gallery_paths'] = $this->record->galleryImages->pluck('image_path')->all();
        }

        return $data;
    }

    protected function syncAnnouncementGallery(): void
    {
        $paths = $this->form->getState()['gallery_paths'] ?? [];
        if (! is_array($paths)) {
            $paths = [];
        }

        $paths = array_values(array_filter($paths));
        $announcement = $this->record;
        $existing = $announcement->galleryImages()->get()->keyBy('image_path');
        $keepIds = [];

        foreach ($paths as $sortOrder => $path) {
            if ($existing->has($path)) {
                $image = $existing->get($path);
                $image->update(['sort_order' => $sortOrder]);
                $keepIds[] = $image->id;
            } else {
                $image = $announcement->galleryImages()->create([
                    'image_path' => $path,
                    'sort_order' => $sortOrder,
                ]);
                $keepIds[] = $image->id;
            }
        }

        if ($keepIds === []) {
            $announcement->galleryImages()->delete();

            return;
        }

        $announcement->galleryImages()->whereNotIn('id', $keepIds)->delete();
    }
}
