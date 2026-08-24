<?php

namespace App\Filament\Resources\AnnouncementResource\Concerns;

trait SyncsAnnouncementGallery
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record?->exists) {
            $this->record->loadMissing('galleryImages');
            $data['gallery_paths'] = $this->record->galleryImages
                ->pluck('image_path')
                ->filter()
                ->values()
                ->all();
        }

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['gallery_paths']);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['gallery_paths']);

        return $data;
    }

    protected function syncAnnouncementGallery(): void
    {
        $raw = $this->data['gallery_paths']
            ?? $this->form->getRawState()['gallery_paths']
            ?? [];

        $paths = $this->normalizeGalleryPaths($raw);
        $announcement = $this->record->fresh() ?? $this->record;
        $existing = $announcement->galleryImages()->get()->keyBy('image_path');
        $keepIds = [];

        foreach ($paths as $sortOrder => $path) {
            if ($existing->has($path)) {
                $image = $existing->get($path);
                if ((int) $image->sort_order !== $sortOrder) {
                    $image->update(['sort_order' => $sortOrder]);
                }
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

    /**
     * @param  mixed  $raw
     * @return list<string>
     */
    protected function normalizeGalleryPaths(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        $paths = [];

        foreach ($raw as $item) {
            if (is_string($item) && $item !== '') {
                $paths[] = ltrim(str_replace('\\', '/', $item), '/');

                continue;
            }

            if (is_object($item) && method_exists($item, 'getClientOriginalName')) {
                // TemporaryUploadedFile — Filament henüz store etmediyse atla; path string beklenir
                continue;
            }

            if (is_array($item)) {
                $candidate = $item['path'] ?? $item['url'] ?? null;
                if (is_string($candidate) && $candidate !== '') {
                    $paths[] = ltrim(str_replace('\\', '/', $candidate), '/');
                }
            }
        }

        return array_values(array_unique($paths));
    }
}
