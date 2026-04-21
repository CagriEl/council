<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $modelLabel = 'Haber';

    protected static ?string $pluralModelLabel = 'Haberler';

    protected static ?string $navigationLabel = 'Haberler';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Haber İçeriği')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Haber Başlığı')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $state) => $set('slug', Str::slug($state)))
                            ->required(),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL Uzantısı (Otomatik)')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'belediye' => 'Belediye',
                                'kultur' => 'Kültür ve Sanat',
                                'spor' => 'Spor',
                                'cevre' => 'Çevre ve Kent',
                                'sosyal' => 'Sosyal Hizmetler',
                            ])
                            ->required()
                            ->default('belediye'),
                        Forms\Components\Textarea::make('summary')
                            ->label('Özet (Kısa Açıklama)')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->label('Haber Metni')
                            ->fileAttachmentsDirectory('news-images')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Görsel ve Ayarlar')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Kapak Görseli')
                            ->image()
                            ->directory('news-covers')
                            ->required(),
                        Forms\Components\DatePicker::make('published_at')
                            ->label('Yayınlanma tarihi')
                            ->helperText('Sitede görünmeye başlayacağı gün.')
                            ->default(now())
                            ->required(),
                        Forms\Components\DatePicker::make('unpublished_at')
                            ->label('Yayından kaldırma tarihi')
                            ->helperText('Bu tarihten itibaren sitede gösterilmez. Boş bırakılırsa süresiz yayında kalır.')
                            ->nullable(),
                        Forms\Components\Toggle::make('is_headline')
                            ->label('Ana Manşette Göster')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')->label('Görsel'),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'belediye' => 'Belediye',
                        'kultur' => 'Kültür ve Sanat',
                        'spor' => 'Spor',
                        'cevre' => 'Çevre ve Kent',
                        'sosyal' => 'Sosyal Hizmetler',
                        default => $state ?? '—',
                    }),
                Tables\Columns\TextColumn::make('title')->label('Başlık')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('published_at')->label('Yayın')->date('d.m.Y')->sortable(),
                Tables\Columns\TextColumn::make('unpublished_at')->label('Kalkış')->date('d.m.Y')->sortable()->placeholder('—'),
                Tables\Columns\IconColumn::make('is_headline')->label('Manşet')->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
