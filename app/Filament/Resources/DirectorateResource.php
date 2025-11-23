<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectorateResource\Pages;
use App\Models\Directorate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set; // <--- KRİTİK DÜZELTME: Doğru Set sınıfı bu
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str; // Slug oluşturmak için gerekli

class DirectorateResource extends Resource
{
    protected static ?string $model = Directorate::class;

    // Türkçe İsimlendirmeler
    protected static ?string $modelLabel = 'Müdürlük';
    protected static ?string $pluralModelLabel = 'Müdürlükler';
    protected static ?string $navigationLabel = 'Müdürlükler';
    protected static ?string $navigationGroup = 'Kurumsal';
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. BÖLÜM: BAĞLI OLDUĞU MAKAM VE İSİM
                Forms\Components\Section::make('Genel Bilgiler')
                    ->schema([
                        // Başkan Yardımcısı Seçimi
                        Forms\Components\Select::make('vice_president_id')
                            ->relationship('vicePresident', 'name') // Modeldeki 'vicePresident' fonksiyonunu kullanır
                            ->label('Bağlı Olduğu Başkan Yardımcısı')
                            ->searchable()
                            ->preload()
                            ->nullable(), // Zorunlu değilse boş bırakılabilir

                        // Müdürlük Adı ve Otomatik Slug
                        Forms\Components\TextInput::make('name')
                            ->label('Müdürlük Adı')
                            ->required()
                            ->live(onBlur: true) // Yazarken canlı dinle
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))), // Slug oluştur

                        // Gizli Slug Alanı (URL için)
                        Forms\Components\TextInput::make('slug')
                            ->label('URL Uzantısı')
                            ->disabled()
                            ->dehydrated() // Disabled olsa da veritabanına kaydet
                            ->required()
                            ->unique(ignoreRecord: true),
                    ])->columns(2),

                // 2. BÖLÜM: MÜDÜR BİLGİLERİ
                Forms\Components\Section::make('Müdür Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('manager_name')
                            ->label('Müdür Adı Soyadı'),
                        
                        Forms\Components\TextInput::make('manager_title')
                            ->label('Unvan')
                            ->default('Müdür V.'),
                        
                        Forms\Components\FileUpload::make('manager_image')
                            ->label('Müdür Fotoğrafı')
                            ->image()
                            ->directory('managers') // storage/app/public/managers klasörüne kaydeder
                            ->columnSpanFull(),
                    ])->columns(2),

                // 3. BÖLÜM: İLETİŞİM VE DETAYLAR
                Forms\Components\Section::make('İletişim ve İçerik')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel(),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('E-Posta')
                            ->email(),

                        Forms\Components\RichEditor::make('description')
                            ->label('Görev, Yetki ve Sorumluluklar')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Müdürlük Adı')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('vicePresident.name')
                    ->label('Bağlı Olduğu Bşk. Yrd.')
                    ->sortable(),

                Tables\Columns\TextColumn::make('manager_name')
                    ->label('Müdür')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon'),
            ])
            ->filters([
                // İsterseniz Başkan Yardımcısına göre filtre ekleyebilirsiniz
                Tables\Filters\SelectFilter::make('vice_president_id')
                    ->label('Başkan Yardımcısına Göre')
                    ->relationship('vicePresident', 'name'),
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
            'index' => Pages\ListDirectorates::route('/'),
            'create' => Pages\CreateDirectorate::route('/create'),
            'edit' => Pages\EditDirectorate::route('/{record}/edit'),
        ];
    }
}