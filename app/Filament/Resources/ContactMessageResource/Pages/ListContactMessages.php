<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use App\Services\ContactSpamGuard;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('purgeSpam')
                ->label('Spamleri temizle')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Spam mesajları sil')
                ->modalDescription('Rate test ve benzeri spam kalıplarına uyan tüm mesajlar silinecek. Devam edilsin mi?')
                ->modalSubmitActionLabel('Spamleri sil')
                ->action(function (ContactSpamGuard $guard): void {
                    $deleted = 0;

                    ContactMessage::query()->orderBy('id')->chunkById(200, function ($messages) use ($guard, &$deleted) {
                        foreach ($messages as $message) {
                            if (! $guard->payloadLooksLikeSpam($message->payload)) {
                                continue;
                            }
                            $message->delete();
                            $deleted++;
                        }
                    });

                    Notification::make()
                        ->title($deleted > 0 ? "{$deleted} spam mesaj silindi" : 'Silinecek spam bulunamadı')
                        ->success()
                        ->send();
                }),
        ];
    }
}
