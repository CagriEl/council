<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommissionResource\Pages;
use App\Filament\Resources\CommissionResource\RelationManagers;
use App\Models\Commission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommissionResource extends Resource
{
    protected static ?string $model = Commission::class;

protected static ?string $modelLabel = 'Komisyon';
protected static ?string $pluralModelLabel = 'Komisyonlar';
protected static ?string $navigationLabel = 'Komisyonlar';
protected static ?string $navigationGroup = 'Meclis Yönetimi';
protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
  
public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Genel Bilgiler')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Komisyon Adı (Örn: İmar Komisyonu)')
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Açıklama')
                        ->rows(2),
                ]),

            Forms\Components\Section::make('Komisyon Üyeleri')
                ->description('Komisyonda görevli meclis üyelerini ekleyin.')
                ->schema([
                    Forms\Components\Repeater::make('members')
                        ->relationship() // Relation manager kullanmadan direkt eklemek için
                        ->schema([
                            Forms\Components\TextInput::make('name')->label('Ad Soyad')->required(),
                            Forms\Components\TextInput::make('title')->label('Görevi')->default('Komisyon Üyesi'),
                            Forms\Components\TextInput::make('party_name')->label('Partisi'),
                        ])
                        ->addActionLabel('Yeni Üye Ekle')
                        ->grid(2) // 2 sütunlu görünüm
                        ->columnSpanFull()
                ])
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')->label('Komisyon Adı'),
            Tables\Columns\TextColumn::make('members_count')->counts('members')->label('Üye Sayısı'),
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
            'index' => Pages\ListCommissions::route('/'),
            'create' => Pages\CreateCommission::route('/create'),
            'edit' => Pages\EditCommission::route('/{record}/edit'),
        ];
    }
}
