<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UniversalFormResource\Pages;
use App\Models\UniversalForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UniversalFormResource extends Resource
{
    protected static ?string $model = UniversalForm::class;

    /** Filament başlık ve breadcrumb için (sınıf adı "Universal Form" görünmesin diye) */
    protected static ?string $modelLabel = 'Form gönderisi';

    protected static ?string $pluralModelLabel = 'Gelen formlar';

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationLabel = 'Gelen Formlar';

    protected static ?string $navigationGroup = 'Formlar';

    protected static ?int $navigationSort = -19;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('source')->label('Form Kaynağı')->readOnly(),
                Forms\Components\TextInput::make('platform')->label('Platform')->readOnly(),
                Forms\Components\KeyValue::make('data')->label('Form Verileri')->readOnly()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime('d.m.Y H:i')->label('Tarih'),
                Tables\Columns\TextColumn::make('source')->badge()->label('Kaynak'), // baskan, iletisim, mobil
                Tables\Columns\TextColumn::make('platform')->badge()->color('gray'), // web, android
                Tables\Columns\TextColumn::make('data.name')->label('Ad Soyad')->placeholder('-'),
                Tables\Columns\TextColumn::make('data.phone')->label('Telefon')->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUniversalForms::route('/'),
            'view' => Pages\ViewUniversalForm::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
