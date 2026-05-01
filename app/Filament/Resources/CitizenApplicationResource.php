<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CitizenApplicationResource\Pages;
use App\Models\CitizenApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CitizenApplicationResource extends Resource
{
    protected static ?string $model = CitizenApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'E-Hizmet Başvuruları';

    protected static ?string $modelLabel = 'E-Hizmet Başvurusu';

    protected static ?string $pluralModelLabel = 'E-Hizmet Başvuruları';

    protected static ?string $navigationGroup = 'Formlar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Başvuru Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('tracking_no')->label('Takip No')->readOnly(),
                        Forms\Components\TextInput::make('service_type')
                            ->label('Hizmet Türü')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'ruhsat' => 'Ruhsat',
                                'e_imar' => 'E-İmar',
                                'evrak' => 'Evrak Takibi / Doğrulama',
                                'sosyal_destek' => 'Sosyal Destek',
                                default => $state,
                            })
                            ->readOnly(),
                        Forms\Components\TextInput::make('full_name')->label('Ad Soyad')->readOnly(),
                        Forms\Components\TextInput::make('identity_no')->label('T.C. Kimlik No')->readOnly(),
                        Forms\Components\TextInput::make('phone')->label('Telefon')->readOnly(),
                        Forms\Components\TextInput::make('email')->label('E-Posta')->readOnly(),
                        Forms\Components\Textarea::make('address')->label('Adres')->rows(3)->readOnly()->columnSpanFull(),
                        Forms\Components\Textarea::make('request_summary')->label('Başvuru Özeti')->rows(6)->readOnly()->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Süreç Yönetimi')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->required()
                            ->options([
                                'received' => 'Alındı',
                                'in_process' => 'İşlemde',
                                'completed' => 'Tamamlandı',
                                'rejected' => 'Reddedildi',
                            ]),
                        Forms\Components\TextInput::make('assigned_unit')
                            ->label('Atanan Birim')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('response_text')
                            ->label('Vatandaşa Dönüş')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('resolved_at')
                            ->label('Sonuçlanma Tarihi'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tracking_no')->label('Takip No')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('service_type')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ruhsat' => 'Ruhsat',
                        'e_imar' => 'E-İmar',
                        'evrak' => 'Evrak',
                        'sosyal_destek' => 'Sosyal Destek',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('full_name')->label('Ad Soyad')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'received' => 'Alındı',
                        'in_process' => 'İşlemde',
                        'completed' => 'Tamamlandı',
                        'rejected' => 'Reddedildi',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'received',
                        'primary' => 'in_process',
                        'success' => 'completed',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('service_type')
                    ->label('Hizmet Türü')
                    ->options([
                        'ruhsat' => 'Ruhsat',
                        'e_imar' => 'E-İmar',
                        'evrak' => 'Evrak',
                        'sosyal_destek' => 'Sosyal Destek',
                    ]),
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'received' => 'Alındı',
                        'in_process' => 'İşlemde',
                        'completed' => 'Tamamlandı',
                        'rejected' => 'Reddedildi',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCitizenApplications::route('/'),
            'view' => Pages\ViewCitizenApplication::route('/{record}'),
            'edit' => Pages\EditCitizenApplication::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
