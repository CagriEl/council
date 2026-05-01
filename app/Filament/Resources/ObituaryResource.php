<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObituaryResource\Pages;
use App\Models\Obituary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ObituaryResource extends Resource
{
    protected static ?string $model = Obituary::class;

    protected static ?string $modelLabel = 'Vefat Kaydı';

    protected static ?string $pluralModelLabel = 'Vefat Kayıtları';

    protected static ?string $navigationLabel = 'Vefat Kayıtları';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->label('Ad Soyad')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('death_date')
                    ->label('Vefat Tarihi')
                    ->required(),
                Forms\Components\TimePicker::make('prayer_time')
                    ->label('Namaz Saati')
                    ->seconds(false)
                    ->required(),
                Forms\Components\TextInput::make('mosque')
                    ->label('Camii')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('burial_place_type')
                    ->label('Defnedilecek Yer')
                    ->required()
                    ->live()
                    ->default('city_cemetery')
                    ->options([
                        'city_cemetery' => 'Şehir Mezarlığı',
                        'other' => 'Diğer',
                    ]),
                Forms\Components\TextInput::make('burial_place_other')
                    ->label('Diğer Defin Yeri')
                    ->maxLength(255)
                    ->visible(fn (Get $get): bool => $get('burial_place_type') === 'other')
                    ->required(fn (Get $get): bool => $get('burial_place_type') === 'other'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Ad Soyad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('death_date')
                    ->label('Vefat Tarihi')
                    ->date('d.m.Y'),
                Tables\Columns\TextColumn::make('prayer_time')
                    ->label('Namaz Saati')
                    ->formatStateUsing(fn (?string $state): string => $state ? substr($state, 0, 5) : '-'),
                Tables\Columns\TextColumn::make('mosque')
                    ->label('Camii')
                    ->searchable(),
                Tables\Columns\TextColumn::make('burial_place_type')
                    ->label('Defin Yeri')
                    ->formatStateUsing(fn (string $state, Obituary $record): string => $state === 'city_cemetery'
                        ? 'Şehir Mezarlığı'
                        : ($record->burial_place_other ?: 'Diğer')),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıralama'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderBy('sort_order')
            ->orderByDesc('death_date')
            ->orderBy('full_name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObituaries::route('/'),
            'create' => Pages\CreateObituary::route('/create'),
            'edit' => Pages\EditObituary::route('/{record}/edit'),
        ];
    }
}
