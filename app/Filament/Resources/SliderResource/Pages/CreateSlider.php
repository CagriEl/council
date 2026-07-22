<?php

namespace App\Filament\Resources\SliderResource\Pages;

use App\Filament\Resources\SliderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSlider extends CreateRecord
{
    protected static string $resource = SliderResource::class;

    /**
     * FileUpload bazen tek dosyayı dizi olarak döndürür; DB string sütunu için normalize ediyoruz.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizeUploadPaths($data);
    }

    private function normalizeUploadPaths(array $data): array
    {
        foreach (['image_path', 'video_path'] as $field) {
            if (! isset($data[$field])) {
                continue;
            }

            if (is_array($data[$field])) {
                $data[$field] = $data[$field][0] ?? null;
            }

            if ($data[$field] === '' || $data[$field] === []) {
                $data[$field] = null;
            }
        }

        return $data;
    }
}
