<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceRequestResource\Pages;
use App\Models\ServiceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationLabel = 'Gelen Mesajlar';

    protected static ?string $modelLabel = 'Mesaj';

    protected static ?string $pluralModelLabel = 'Mesajlar';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canView($record): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Talep Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('tracking_no')
                            ->label('Takip No')
                            ->readOnly(),
                        Forms\Components\TextInput::make('source')
                            ->label('Kaynak')
                            ->readOnly(),
                        Forms\Components\TextInput::make('platform')
                            ->label('Platform')
                            ->readOnly(),
                        Forms\Components\TextInput::make('full_name')
                            ->label('Ad Soyad')
                            ->readOnly(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->readOnly(),
                        Forms\Components\TextInput::make('email')
                            ->label('E-Posta')
                            ->readOnly(),
                        Forms\Components\TextInput::make('subject')
                            ->label('Konu')
                            ->readOnly()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Talep Açıklaması')
                            ->rows(6)
                            ->readOnly()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('İşlem Süreci')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->required()
                            ->options([
                                'open' => 'Açık',
                                'in_review' => 'İncelemede',
                                'resolved' => 'Çözüldü',
                                'rejected' => 'Reddedildi',
                            ]),
                        Forms\Components\TextInput::make('assigned_unit')
                            ->label('Atanan Birim')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('response_text')
                            ->label('Vatandaşa Dönüş')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('admin_note')
                            ->label('İç Not')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('resolved_at')
                            ->label('Çözüm Tarihi'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tracking_no')
                    ->label('Takip No')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Ad Soyad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('source')
                    ->label('Kaynak')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'iletisim-sayfasi' => 'İletişim',
                        'baskan-sayfasi' => 'Başkan',
                        'talep-sikayet-sayfasi' => 'Talep/Şikayet',
                        'kvkk-sayfasi' => 'KVKK sayfası',
                        default => $state ?: '-',
                    }),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Konu')
                    ->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Açık',
                        'in_review' => 'İncelemede',
                        'resolved' => 'Çözüldü',
                        'rejected' => 'Reddedildi',
                        default => $state,
                    })
                    ->colors([
                        'danger' => 'open',
                        'warning' => 'in_review',
                        'success' => 'resolved',
                        'gray' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('assigned_unit')
                    ->label('Birim')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('source')
                    ->label('Kaynak')
                    ->options([
                        'iletisim-sayfasi' => 'İletişim',
                        'baskan-sayfasi' => 'Başkan',
                        'talep-sikayet-sayfasi' => 'Talep/Şikayet',
                        'kvkk-sayfasi' => 'KVKK sayfası',
                    ]),
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'open' => 'Açık',
                        'in_review' => 'İncelemede',
                        'resolved' => 'Çözüldü',
                        'rejected' => 'Reddedildi',
                    ]),
            ])
            ->recordUrl(fn (ServiceRequest $record): string => Pages\EditServiceRequest::getUrl(['record' => $record]))
            ->actions([
                Tables\Actions\EditAction::make()->label('İşlem Yap'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRequests::route('/'),
            'view' => Pages\ViewServiceRequest::route('/{record}'),
            'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
