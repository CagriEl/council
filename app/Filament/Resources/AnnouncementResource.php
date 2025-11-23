<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Filament\Resources\AnnouncementResource\RelationManagers;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                        ->label('Tarih')
                        ->default(now())
                        ->required(),
                    Forms\Components\RichEditor::make('content')
                        ->label('İçerik')
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('file_path')
                        ->label('Ek Dosya (PDF)')
                        ->directory('announcements')
                        ->acceptedFileTypes(['application/pdf'])
                        ->columnSpanFull(),
                ])->columns(2)
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('title')->label('Başlık')->limit(50)->searchable(),
            Tables\Columns\TextColumn::make('type')
                ->label('Tip')
                ->badge()
                ->colors([
                    'primary' => 'duyuru',
                    'warning' => 'resmi',
                    'danger' => 'ihale',
                ]),
            Tables\Columns\TextColumn::make('date')->label('Tarih')->date('d.m.Y'),
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
