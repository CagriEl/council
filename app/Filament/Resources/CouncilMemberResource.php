<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouncilMemberResource\Pages;
use App\Filament\Resources\CouncilMemberResource\RelationManagers;
use App\Models\CouncilMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouncilMemberResource extends Resource
{
    protected static ?string $model = CouncilMember::class;

protected static ?string $modelLabel = 'Meclis Üyesi';
protected static ?string $pluralModelLabel = 'Meclis Üyeleri';
protected static ?string $navigationLabel = 'Meclis Üyeleri';
protected static ?string $navigationGroup = 'Meclis Yönetimi';
protected static ?string $navigationIcon = 'heroicon-o-user-group';
  public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label('Ad Soyad')
                ->required(),
            Forms\Components\Select::make('political_party_id')
                ->relationship('politicalParty', 'name')
                ->label('Siyasi Parti')
                ->required()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')->label('Parti Adı')->required(),
                    Forms\Components\ColorPicker::make('color_code')->label('Renk'),
                ]),
            Forms\Components\TextInput::make('title')
                ->label('Unvan')
                ->default('Meclis Üyesi'),
            Forms\Components\FileUpload::make('image_path')
                ->label('Fotoğraf')
                ->avatar()
                ->directory('council-members'),
            Forms\Components\TextInput::make('order')
                ->label('Sıralama')
                ->numeric()
                ->default(0),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\ImageColumn::make('image_path')->label('Foto')->circular(),
            Tables\Columns\TextColumn::make('name')->label('Ad Soyad')->searchable(),
            Tables\Columns\TextColumn::make('politicalParty.name')
                ->label('Partisi')
                ->badge()
                ->color(fn ($record) => \Filament\Support\Colors\Color::hex($record->politicalParty->color_code ?? '#000000')),
            Tables\Columns\TextColumn::make('title')->label('Unvan'),
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
            'index' => Pages\ListCouncilMembers::route('/'),
            'create' => Pages\CreateCouncilMember::route('/create'),
            'edit' => Pages\EditCouncilMember::route('/{record}/edit'),
        ];
    }
}
