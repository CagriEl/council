<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Actions\SendMobilePushAction;
use App\Filament\Resources\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SendMobilePushAction::make('announcement'),
            Actions\DeleteAction::make(),
        ];
    }
}
