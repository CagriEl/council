<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use App\Filament\Resources\AnnouncementResource\Concerns\SyncsAnnouncementGallery;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnouncement extends CreateRecord
{
    use SyncsAnnouncementGallery;

    protected static string $resource = AnnouncementResource::class;

    protected function afterCreate(): void
    {
        $this->syncAnnouncementGallery();
    }
}
