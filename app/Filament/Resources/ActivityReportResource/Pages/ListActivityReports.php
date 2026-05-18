<?php

namespace App\Filament\Resources\ActivityReportResource\Pages;

use App\Filament\Resources\ActivityReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityReports extends ListRecords
{
    protected static string $resource = ActivityReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
