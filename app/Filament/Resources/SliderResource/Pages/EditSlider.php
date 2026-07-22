<?php

namespace App\Filament\Resources\SliderResource\Pages;

use App\Filament\Resources\SliderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSlider extends EditRecord
{
    protected static string $resource = SliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * FileUpload state'i her zaman dizi bekler; string gelirse validation TypeError → 500 olur.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach (['image_path', 'video_path'] as $field) {
            if (! array_key_exists($field, $data)) {
                continue;
            }

            if (is_string($data[$field]) && $data[$field] !== '') {
                // Filament afterStateHydrated Arr::wrap yapar; yine de garantiye alıyoruz
                $data[$field] = $data[$field];
            }

            if ($data[$field] === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->normalizeUploadPaths($data);
    }

    private function normalizeUploadPaths(array $data): array
    {
        foreach (['image_path', 'video_path'] as $field) {
            if (! array_key_exists($field, $data)) {
                continue;
            }

            if (is_array($data[$field])) {
                $data[$field] = array_values(array_filter($data[$field]))[0] ?? null;
            }

            if ($data[$field] === '' || $data[$field] === []) {
                $data[$field] = null;
            }
        }

        return $data;
    }
}
