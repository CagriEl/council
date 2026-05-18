<?php

namespace App\Filament\Resources\CouncilDecisionResource\Pages;

use App\Filament\Resources\CouncilDecisionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCouncilDecision extends EditRecord
{
    protected static string $resource = CouncilDecisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
