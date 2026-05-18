<?php

namespace App\Filament\Resources\CouncilMeetingResource\Pages;

use App\Filament\Resources\CouncilMeetingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCouncilMeetings extends ListRecords
{
    protected static string $resource = CouncilMeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
