<?php

namespace App\Filament\Resources\PoliticalPartyResource\Pages;

use App\Filament\Resources\PoliticalPartyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPoliticalParty extends EditRecord
{
    protected static string $resource = PoliticalPartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
