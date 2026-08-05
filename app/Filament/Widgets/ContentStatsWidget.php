<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AnnouncementResource;
use App\Filament\Resources\ContactMessageResource;
use App\Filament\Resources\NewsResource;
use App\Models\Announcement;
use App\Models\ContactMessage;
use App\Models\News;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentStatsWidget extends BaseWidget
{
    protected static ?int $sort = -10;

    protected static ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        return NewsResource::canViewAny()
            || AnnouncementResource::canViewAny()
            || ContactMessageResource::canViewAny();
    }

    protected function getHeading(): ?string
    {
        return 'İçerik istatistikleri';
    }

    protected function getStats(): array
    {
        $stats = [];

        if (NewsResource::canViewAny()) {
            $stats[] = Stat::make('Haberler', (string) News::query()->count())
                ->description('Toplam haber kaydı')
                ->descriptionIcon('heroicon-o-newspaper')
                ->color('success')
                ->url(NewsResource::getUrl('index'));
        }

        if (AnnouncementResource::canViewAny()) {
            $stats[] = Stat::make('Duyurular', (string) Announcement::query()->count())
                ->description('Toplam duyuru kaydı')
                ->descriptionIcon('heroicon-o-megaphone')
                ->color('info')
                ->url(AnnouncementResource::getUrl('index'));
        }

        if (ContactMessageResource::canViewAny()) {
            $stats[] = Stat::make('İletişim formları', (string) ContactMessage::query()->count())
                ->description('Gelen iletişim mesajları')
                ->descriptionIcon('heroicon-o-envelope')
                ->color('warning')
                ->url(ContactMessageResource::getUrl('index'));
        }

        return $stats;
    }
}
