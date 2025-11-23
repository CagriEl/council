<?php

namespace App\Filament\Resources\CouncilMemberResource\Pages;

use App\Filament\Resources\CouncilMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCouncilMember extends EditRecord
{
    protected static string $resource = CouncilMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
