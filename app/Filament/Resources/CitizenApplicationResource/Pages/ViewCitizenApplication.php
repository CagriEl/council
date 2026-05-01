<?php

namespace App\Filament\Resources\CitizenApplicationResource\Pages;

use App\Filament\Resources\CitizenApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCitizenApplication extends ViewRecord
{
    protected static string $resource = CitizenApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
