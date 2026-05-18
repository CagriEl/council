<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MayorResource\Pages;
use App\Models\Mayor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table; // BU SATIR EKSİK OLDUĞU İÇİN HATA ALIYORDUNUZ

class MayorResource extends Resource
{
    protected static ?string $model = Mayor::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Başkan Yönetimi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Başkan Bilgileri')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Ad Soyad')
                        ->required(),
                    
                    Forms\Components\TextInput::make('title')
                        ->label('Unvan')
                        ->default('Belediye Başkanı'),

                    Forms\Components\FileUpload::make('image_path')
                        ->label('Fotoğraf')
                        ->image()
                        ->directory('mayors'),

                    Forms\Components\RichEditor::make('description')
                        ->label('Biyografi')
                        ->columnSpanFull(),
                        Forms\Components\RichEditor::make('message')
                ->label('Başkanın Mesajı')
                ->columnSpanFull(), // Tam genişlikte görünmesi için

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
                Tables\Columns\ImageColumn::make('image_path')->label('Fotoğraf')->circular(),
                Tables\Columns\TextColumn::make('name')->label('Ad Soyad')->searchable(),
                Tables\Columns\TextColumn::make('title')->label('Unvan'),
                Tables\Columns\IconColumn::make('is_active')->label('Durum')->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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