<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Çıktı Al')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn (): string => route('admin.contact-messages.print', $this->record))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()->label('Sil'),
        ];
    }
}