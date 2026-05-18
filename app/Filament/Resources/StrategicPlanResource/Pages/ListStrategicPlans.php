<?php

namespace App\Filament\Resources\StrategicPlanResource\Pages;

use App\Filament\Resources\StrategicPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStrategicPlans extends ListRecords
{
    protected static string $resource = StrategicPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
