<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MayorResource\Pages;
use App\Filament\Resources\MayorResource\RelationManagers;
use App\Models\Mayor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MayorResource extends Resource
{
    protected static ?string $model = Mayor::class;

protected static ?string $modelLabel = 'Başkan';
protected static ?string $pluralModelLabel = 'Başkan Bilgisi';
protected static ?string $navigationLabel = 'Başkan';
protected static ?string $navigationGroup = 'Kurumsal';
protected static ?string $navigationIcon = 'heroicon-o-user';
   public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label('Başkan Adı Soyadı')
                ->required(),
            Forms\Components\FileUpload::make('image_path')
                ->label('Başkan Fotoğrafı')
                ->image()
                ->directory('mayor'),
            Forms\Components\RichEditor::make('biography')
                ->label('Özgeçmiş')
                ->columnSpanFull(),
            Forms\Components\RichEditor::make('message')
                ->label('Başkanın Mesajı')
                ->columnSpanFull(),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\ImageColumn::make('image_path')->label('Foto')->circular(),
            Tables\Columns\TextColumn::make('name')->label('Ad Soyad'),
        ])
        ->paginated(false); // Tek kayıt olduğu için sayfalama kapalı
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
            'index' => Pages\ListMayors::route('/'),
            'create' => Pages\CreateMayor::route('/create'),
            'edit' => Pages\EditMayor::route('/{record}/edit'),
        ];
    }
}
