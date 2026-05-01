<?php

namespace App\Filament\Resources\ObituaryResource\Pages;

use App\Filament\Resources\ObituaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditObituary extends EditRecord
{
    protected static string $resource = ObituaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
