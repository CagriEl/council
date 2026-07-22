<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
                    ->description('Ana sayfa arka plan görseli. İsterseniz video da ekleyebilirsiniz.')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Kapak Görseli / Resim')
                            ->disk('public')
                            ->directory('sliders')
                            // visibility/public + mimetypes bazı sunucularda save anında 500 veriyor
                            ->visibility('private')
                            ->fetchFileInformation(false)
                            ->nullable()
                            ->helperText('JPG/PNG/WEBP yükleyin. Kaydet’e basınca diske yazılır.')
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): ?string {
                                try {
                                    return $file->store('sliders', 'public');
                                } catch (\Throwable $e) {
                                    report($e);

                                    return null;
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('video_path')
                            ->label('Video (İsteğe Bağlı)')
                            ->disk('public')
                            ->directory('sliders/videos')
                            ->visibility('private')
                            ->fetchFileInformation(false)
                            ->nullable()
                            ->helperText('MP4/WEBM (isteğe bağlı).')
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): ?string {
                                try {
                                    return $file->store('sliders/videos', 'public');
                                } catch (\Throwable $e) {
                                    report($e);

                                    return null;
                                }
                            })
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Detaylar')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Başlık (Opsiyonel)'),

                        Forms\Components\TextInput::make('link')
                            ->label('Yönlendirilecek Link (Opsiyonel)')
                            ->nullable()
                            // ->url() boş/geçersiz değerde kaydı düşürmesin diye kaldırıldı
                            ,

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
                    ->height(60)
                    ->width(100),

                Tables\Columns\IconColumn::make('video_path')
                    ->label('Video')
                    ->boolean()
                    ->trueIcon('heroicon-o-video-camera')
                    ->falseIcon('heroicon-o-x-mark')
                    ->getStateUsing(fn (Slider $record): bool => filled($record->video_path)),

                Tables\Columns\TextColumn::make('title')->label('Başlık')->searchable(),
                Tables\Columns\TextColumn::make('order')->label('Sıra')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Durum'),
            ])
            ->reorderable('order')
            ->defaultSort('order');
    }

    public static function getRelations(): array
    {
        return [];
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
