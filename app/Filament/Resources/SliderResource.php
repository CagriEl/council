<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

protected static ?string $modelLabel = 'Manşet';
protected static ?string $pluralModelLabel = 'Manşetler';
protected static ?string $navigationLabel = 'Ana Manşet';
protected static ?string $navigationGroup = 'Site Ayarları';
protected static ?string $navigationIcon = 'heroicon-o-photo';
    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\FileUpload::make('image_path')
                ->label('Slider Görseli (1920x1080 önerilir)')
                ->image()
                ->directory('sliders')
                ->required()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('title')
                ->label('Başlık (Opsiyonel)'),
            Forms\Components\TextInput::make('link')
                ->label('Yönlendirilecek Link (Opsiyonel)')
                ->url(),
            Forms\Components\TextInput::make('order')
                ->label('Sıralama')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('is_active')
                ->label('Yayında')
                ->default(true),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\ImageColumn::make('image_path')->label('Görsel')->width(100),
            Tables\Columns\TextColumn::make('title')->label('Başlık'),
            Tables\Columns\TextColumn::make('order')->label('Sıra')->sortable(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Durum'),
        ])
        ->reorderable('order') // Sürükle bırak sıralama
        ->defaultSort('order');
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
