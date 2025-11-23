<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PoliticalPartyResource\Pages;
use App\Filament\Resources\PoliticalPartyResource\RelationManagers;
use App\Models\PoliticalParty;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PoliticalPartyResource extends Resource
{
    protected static ?string $model = PoliticalParty::class;

protected static ?string $modelLabel = 'Siyasi Parti';
protected static ?string $pluralModelLabel = 'Siyasi Partiler';
protected static ?string $navigationLabel = 'Parti Tanımları';
protected static ?string $navigationGroup = 'Meclis Yönetimi';
protected static ?string $navigationIcon = 'heroicon-o-flag';
    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label('Parti Adı (Örn: AK Parti)')
                ->required(),
            Forms\Components\ColorPicker::make('color_code')
                ->label('Parti Rengi (Rozet için)')
                ->required(),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')->label('Parti Adı'),
            Tables\Columns\ColorColumn::make('color_code')->label('Renk'),
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
            'index' => Pages\ListPoliticalParties::route('/'),
            'create' => Pages\CreatePoliticalParty::route('/create'),
            'edit' => Pages\EditPoliticalParty::route('/{record}/edit'),
        ];
    }
}
