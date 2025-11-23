<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VicePresidentResource\Pages;
use App\Filament\Resources\VicePresidentResource\RelationManagers;
use App\Models\VicePresident;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VicePresidentResource extends Resource
{
    protected static ?string $model = VicePresident::class;

protected static ?string $modelLabel = 'Başkan Yardımcısı';
protected static ?string $pluralModelLabel = 'Başkan Yardımcıları';
protected static ?string $navigationLabel = 'Başkan Yardımcıları';
protected static ?string $navigationGroup = 'Kurumsal';
protected static ?string $navigationIcon = 'heroicon-o-users';
  

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label('Ad Soyad')
                ->required(),

            Forms\Components\TextInput::make('title')
                ->label('Unvan')
                ->default('Belediye Başkan Yardımcısı'),

            Forms\Components\FileUpload::make('image_path')
                ->label('Fotoğraf')
                ->image()
                ->directory('vice-presidents'),
                // ->required() // İsterseniz bunu kaldırabilirsiniz, fotoğrafsız da kaydetsin.

            // HATA BURADAYDI, DÜZELTİLDİ:
            Forms\Components\TextInput::make('order')
                ->label('Sıralama')
                ->numeric()
                ->default(0)   // Varsayılan değer 0 olsun
                ->required(),  // Boş bırakılamaz (böylece null hatası vermez)
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\ImageColumn::make('image_path')->circular(),
            Tables\Columns\TextColumn::make('name')->label('Ad Soyad'),
            Tables\Columns\TextColumn::make('directorates_count')
                ->counts('directorates')
                ->label('Bağlı Müdürlük Sayısı'),
        ])
        ->reorderable('order');
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
            'index' => Pages\ListVicePresidents::route('/'),
            'create' => Pages\CreateVicePresident::route('/create'),
            'edit' => Pages\EditVicePresident::route('/{record}/edit'),
        ];
    }
}
