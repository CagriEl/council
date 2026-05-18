<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuickLinkResource\Pages;
use App\Filament\Resources\QuickLinkResource\RelationManagers;
use App\Models\QuickLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuickLinkResource extends Resource
{
    protected static ?string $model = QuickLink::class;

protected static ?string $modelLabel = 'Hızlı Link';
protected static ?string $pluralModelLabel = 'Hızlı Linkler';
protected static ?string $navigationLabel = 'Kare Butonlar';
protected static ?string $navigationGroup = 'Site Ayarları';
protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-rays';
 public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('title')
                ->label('Buton Başlığı (Örn: E-İmar)')
                ->required(),
            Forms\Components\TextInput::make('url')
                ->label('Link Adresi')
                ->url()
                ->required(),
            Forms\Components\TextInput::make('icon_class')
                ->label('FontAwesome İkon Kodu')
                ->placeholder('fas fa-city')
                ->helperText('Örn: fas fa-home, fas fa-user. FontAwesome 6 ikonlarını kullanın.')
                ->required(),
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
            Tables\Columns\TextColumn::make('icon_class')
                ->label('İkon')
                ->formatStateUsing(fn (string $state): string => new \Illuminate\Support\HtmlString("<i class='{$state}'></i>")),
            Tables\Columns\TextColumn::make('title')->label('Başlık'),
            Tables\Columns\TextColumn::make('url')->label('Link'),
            Tables\Columns\TextColumn::make('order')->label('Sıra'),
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
            'index' => Pages\ListQuickLinks::route('/'),
            'create' => Pages\CreateQuickLink::route('/create'),
            'edit' => Pages\EditQuickLink::route('/{record}/edit'),
        ];
    }
}
