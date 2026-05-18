<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                Forms\Components\Section::make('Görsel ve Video')
                    ->description('Ana sayfada görünecek arka plan. Video yüklerseniz görsel "kapak resmi" (poster) olarak kullanılır.')
                    ->schema([
                        // Resim Yükleme
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Kapak Görseli / Resim')
                            ->image()
                            ->disk('public')
                            ->directory('sliders')
                            ->visibility('public')
                            ->nullable()
                            ->columnSpanFull(),

                        // Video Yükleme (YENİ EKLENDİ)
                        Forms\Components\FileUpload::make('video_path')
                            ->label('Video (İsteğe Bağlı)')
                            ->disk('public')
                            ->directory('sliders/videos')
                            ->visibility('public')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime']) // Sadece video formatları
                            ->maxSize(204800) // Maksimum 100MB (102400 KB)
                            ->columnSpanFull()
                    ]),

                Forms\Components\Section::make('Detaylar')
                    ->schema([
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
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Görsel')
                    ->disk('public')
                    ->width(100),
                
                Tables\Columns\IconColumn::make('video_path')
                    ->label('Video Var mı?')
                    ->boolean()
                    ->trueIcon('heroicon-o-video-camera')
                    ->falseIcon('heroicon-o-x-mark'),

                Tables\Columns\TextColumn::make('title')->label('Başlık'),
                Tables\Columns\TextColumn::make('order')->label('Sıra')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Durum'),
            ])
            ->reorderable('order')
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