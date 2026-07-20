<?php

namespace App\Filament\Actions;

use App\Support\Push\ExpoPushService;
use Filament\Actions\Action as HeaderAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action as TableAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SendMobilePushAction
{
    /**
     * @param  class-string  $contentType  announcement|news
     */
    public static function make(string $contentType): HeaderAction
    {
        return self::configure(HeaderAction::make('sendMobilePush'), $contentType);
    }

    /**
     * @param  class-string  $contentType  announcement|news
     */
    public static function table(string $contentType): TableAction
    {
        return self::configure(TableAction::make('sendMobilePush'), $contentType);
    }

    /**
     * @param  HeaderAction|TableAction  $action
     */
    private static function configure(HeaderAction|TableAction $action, string $contentType): HeaderAction|TableAction
    {
        $label = $contentType === 'news' ? 'Haber Bildirimi Gönder' : 'Duyuru Bildirimi Gönder';

        return $action
            ->label('Bildirim Gönder')
            ->icon('heroicon-o-bell-alert')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading($label)
            ->modalDescription('Kayıtlı mobil cihazlara push bildirimi gönderilir.')
            ->form([
                Textarea::make('body')
                    ->label('Bildirim metni')
                    ->rows(3)
                    ->required()
                    ->maxLength(500)
                    ->default(fn (?Model $record): string => self::defaultBody($record, $contentType)),
            ])
            ->action(function (Model $record, array $data, ExpoPushService $pushService) use ($contentType): void {
                $result = $contentType === 'news'
                    ? $pushService->sendNews($record, $data['body'])
                    : $pushService->sendAnnouncement($record, $data['body']);

                if ($result['total'] === 0) {
                    Notification::make()
                        ->title('Kayıtlı cihaz yok')
                        ->body('Henüz mobil uygulamadan bildirim izni veren cihaz bulunmuyor.')
                        ->warning()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title('Bildirim gönderildi')
                    ->body("{$result['sent']} başarılı, {$result['failed']} başarısız (toplam {$result['total']} cihaz).")
                    ->success()
                    ->send();
            });
    }

    private static function defaultBody(?Model $record, string $contentType): string
    {
        if (! $record) {
            return '';
        }

        if ($contentType === 'news') {
            return Str::limit(strip_tags((string) ($record->summary ?: $record->content)), 160);
        }

        return Str::limit(strip_tags((string) $record->content), 160);
    }
}
