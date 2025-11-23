<?php

namespace App\Filament\Resources\CouncilMeetingResource\Pages;

use App\Filament\Resources\CouncilMeetingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCouncilMeeting extends EditRecord
{
    protected static string $resource = CouncilMeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
