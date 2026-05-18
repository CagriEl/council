<?php

namespace App\Filament\Resources\VicePresidentResource\Pages;

use App\Filament\Resources\VicePresidentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVicePresident extends EditRecord
{
    protected static string $resource = VicePresidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
