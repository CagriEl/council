<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use App\Models\Page; // Sayfaları çekmek için Page modelini ekledik
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationLabel = 'Menü Yönetimi';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Menü Detayları')->schema([
                    
                    // --- 1. YENİ EKLENEN AKILLI SEÇİM ALANI ---
                    Forms\Components\Select::make('menu_source')
                        ->label('Hızlı Bağlantı Seç (İsteğe Bağlı)')
                        ->searchable()
                        ->live() // Seçim yapıldığında anında tetikle
                        ->options(function () {
                            // 1. Sabit / Özel Sayfalar Listesi
                            $options = [
                                'Özel Sayfalar' => [
                                    '/' => 'Ana Sayfa',
                                    '/baskan' => 'Başkan',
                                    '/meclis' => 'Meclis',
                                    '/haberler' => 'Haberler',
                                    '/duyurular' => 'Duyurular',
                                    '/iletisim' => 'İletişim',
                                    '/projeler' => 'Projeler',
                                    '/etkinlikler' => 'Etkinlikler',
                                ]
                            ];

                            // 2. Dinamik Kurumsal Sayfalar (Page Modeli Varsa)
                            if (class_exists(\App\Models\Page::class)) {
                                $pages = \App\Models\Page::where('is_active', true)->pluck('title', 'slug')->toArray();
                                
                                if (count($pages) > 0) {
                                    $formattedPages = [];
                                    foreach ($pages as $slug => $title) {
                                        // URL formatını oluşturuyoruz: /sayfa/vizyonumuz
                                        $formattedPages["/sayfa/$slug"] = $title;
                                    }
                                    $options['Kurumsal Sayfalar'] = $formattedPages;
                                }
                            }

                            return $options;
                        })
                        ->afterStateUpdated(function ($state, Forms\Set $set, $component) {
                            // Seçim yapıldığında çalışır
                            if ($state) {
                                // 1. Link alanını doldur
                                $set('url', $state);

                                // 2. Başlığı bulup doldur
                                // Seçenekler listesinden seçilen URL'ye karşılık gelen Başlığı buluyoruz
                                $options = $component->getOptions();
                                foreach ($options as $group => $items) {
                                    if (isset($items[$state])) {
                                        $set('title', $items[$state]);
                                        break;
                                    }
                                }
                            }
                        })
                        ->placeholder('Listeden bir sayfa veya link seçin...')
                        ->helperText('Seçim yaptığınızda Başlık ve Link alanları otomatik dolar. İsterseniz sonrasında düzenleyebilirsiniz.')
                        ->columnSpanFull(),

                    // --- 2. MENÜ BAŞLIĞI ---
                    Forms\Components\TextInput::make('title')
                        ->label('Menü Başlığı')
                        ->required()
                        ->placeholder('Örn: Hakkımızda veya Başkan')
                        ->columnSpanFull(),

                    // --- 3. URL ---
                    Forms\Components\TextInput::make('url')
                        ->label('Link / URL Adresi')
                        ->required()
                        ->placeholder('/sayfa/vizyonumuz')
                        ->helperText('Otomatik dolabilir veya manuel girebilirsiniz. Dış linkler için https:// kullanın.')
                        ->prefix(url('/'))
                        ->columnSpanFull(),

                    // --- 4. DİĞER AYARLAR ---
                    Forms\Components\Select::make('location')
                        ->label('Konum')
                        ->options([
                            'header' => 'Üst Menü (Header)',
                            'footer' => 'Alt Menü (Footer)',
                        ])
                        ->default('header')
                        ->required(),

                    Forms\Components\Select::make('parent_id')
                        ->label('Üst Menü (Dropdown Yapılacaksa)')
                        ->options(function (?Menu $record) {
                            $query = Menu::query();
                            if ($record) {
                                $query->where('id', '!=', $record->id);
                            }
                            return $query->pluck('title', 'id');
                        })
                        ->searchable()
                        ->placeholder('Ana Menü Öğesi (Yok)'),

                    Forms\Components\TextInput::make('order')
                        ->label('Sıralama')
                        ->numeric()
                        ->default(0),

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
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('url')
                    ->label('Link')
                    ->limit(40)
                    ->color('primary')
                    ->copyable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Konum')
                    ->badge()
                    ->colors(['primary' => 'header', 'warning' => 'footer']),

                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Üst Menü')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('order')
                    ->label('Sıra')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Durum'),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('location')
                    ->options(['header'=>'Header', 'footer'=>'Footer']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}