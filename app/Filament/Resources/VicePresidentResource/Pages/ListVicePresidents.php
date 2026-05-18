<?php

namespace App\Filament\Resources\VicePresidentResource\Pages;

use App\Filament\Resources\VicePresidentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVicePresidents extends ListRecords
{
    protected static string $resource = VicePresidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
