<?php

namespace App\Filament\Resources\UniversalFormResource\Pages;

use App\Filament\Resources\UniversalFormResource;
use Filament\Resources\Pages\ListRecords;

class ListUniversalForms extends ListRecords
{
    protected static string $resource = UniversalFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Ekleme butonu gerekirse burayı açabilirsiniz
            // Actions\CreateAction::make(),
        ];
    }
}
