<?php

namespace App\Filament\Resources\CouncilDecisionResource\Pages;

use App\Filament\Resources\CouncilDecisionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCouncilDecisions extends ListRecords
{
    protected static string $resource = CouncilDecisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
