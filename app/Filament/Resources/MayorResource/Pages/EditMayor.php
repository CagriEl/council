<?php

namespace App\Filament\Resources\MayorResource\Pages;

use App\Filament\Resources\MayorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMayor extends EditRecord
{
    protected static string $resource = MayorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
