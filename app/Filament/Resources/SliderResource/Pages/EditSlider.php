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

    protected function mutateFormDataBeforeSave(array $data): array
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
