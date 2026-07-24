<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendMobilePushAction;
use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $modelLabel = 'Duyuru';

    protected static ?string $pluralModelLabel = 'Duyurular';

    protected static ?string $navigationLabel = 'Duyurular';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Duyuru Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Duyuru Başlığı')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $state) => $set('slug', Str::slug($state)))
                            ->required(),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL')
                            ->disabled()
                            ->dehydrated()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('type')
                            ->label('Duyuru Tipi')
                            ->options([
                                'duyuru' => 'Genel Duyuru',
                                'resmi' => 'Resmi İlan',
                                'ihale' => 'İhale Duyurusu',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('date')
                            ->label('Duyuru / ilan tarihi')
                            ->helperText('Belgede veya metinde gösterilecek resmî tarih.')
                            ->default(now())
                            ->required(),
                        Forms\Components\DatePicker::make('published_at')
                            ->label('Sitede yayınlanma tarihi')
                            ->helperText('Boş bırakılırsa “Duyuru tarihi” kullanılır.')
                            ->nullable(),
                        Forms\Components\DatePicker::make('unpublished_at')
                            ->label('Sitede yayından kaldırma tarihi')
                            ->helperText('Bu tarihten sonra ön yüzde listelenmez ve açılmaz. Boş: süresiz.')
                            ->nullable(),
                        Forms\Components\RichEditor::make('content')
                            ->label('İçerik')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Kapak Görseli')
                            ->image()
                            ->directory('announcements/covers')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Ek Dosya (PDF / ZIP)')
                            ->directory('announcements')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/zip',
                                'application/x-zip-compressed',
                                'application/x-zip',
                            ])
                            ->helperText('PDF veya ZIP yükleyebilirsiniz.')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')->label('Görsel'),
                Tables\Columns\TextColumn::make('title')->label('Başlık')->limit(50)->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->colors([
                        'primary' => 'duyuru',
                        'warning' => 'resmi',
                        'danger' => 'ihale',
                    ]),
                Tables\Columns\TextColumn::make('date')->label('İlan tarihi')->date('d.m.Y'),
                Tables\Columns\TextColumn::make('published_at')->label('Yayın')->date('d.m.Y')->placeholder('—'),
                Tables\Columns\TextColumn::make('unpublished_at')->label('Kalkış')->date('d.m.Y')->placeholder('—'),
            ])
            ->actions([
                SendMobilePushAction::table('announcement'),
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
