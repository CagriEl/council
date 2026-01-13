<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str; 

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Kurumsal Sayfalar';
    protected static ?string $modelLabel = 'Sayfa';
    protected static ?int $navigationSort = 2; // Menüdeki sırası

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sayfa İçeriği')->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Sayfa Başlığı')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                            $operation === 'create' ? $set('slug', Str::slug($state)) : null
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->label('URL Bağlantısı (Slug)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Örn: hakkimizda, vizyonumuz (Otomatik oluşur)'),

                    Forms\Components\FileUpload::make('image_path')
                        ->label('Kapak Görseli (İsteğe Bağlı)')
                        ->image()
                        ->directory('pages') // storage/app/public/pages klasörüne kaydeder
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('content')
                        ->label('Detaylı İçerik')
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Yayında')
                        ->default(true),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Görsel')
                    ->square(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('URL')
                    ->color('gray')
                    ->copyable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->date('d.m.Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}