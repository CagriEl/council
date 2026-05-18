<?php

namespace App\Filament\Resources\PoliticalPartyResource\Pages;

use App\Filament\Resources\PoliticalPartyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPoliticalParties extends ListRecords
{
    protected static string $resource = PoliticalPartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
