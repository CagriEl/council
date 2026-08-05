<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationLabel = 'Gelen Mesajlar';
    protected static ?string $modelLabel = 'Mesaj';
    protected static ?string $pluralModelLabel = 'Mesajlar';
    protected static ?string $navigationGroup = 'Formlar';
    protected static ?int $navigationSort = -20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- 1. BÖLÜM: TEKNİK DETAYLAR (Sol/Sağ veya Üst) ---
                Forms\Components\Section::make('Gönderim Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('source')
                            ->label('Kaynak Sayfa')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'iletisim-sayfasi' => 'İletişim Sayfası',
                                'baskan-sayfasi'   => 'Başkan Sayfası',
                                'mobil-app'        => 'Mobil Uygulama',
                                default            => $state,
                            })
                            ->readOnly(),
                        Forms\Components\TextInput::make('platform')
                            ->label('Platform')
                            ->readOnly(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Tarih')
                            ->readOnly(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Adresi')
                            ->readOnly(),
                    ])->columns(4), // Yan yana 4 kutu

                // --- 2. BÖLÜM: KİŞİSEL BİLGİLER (JSON içinden çekiyoruz) ---
                Forms\Components\Section::make('Kişi Bilgileri')
                    ->schema([
                        // 'payload.name' diyerek JSON içindeki 'name' anahtarına ulaşıyoruz
                        Forms\Components\TextInput::make('payload.name')
                            ->label('Ad Soyad')
                            ->readOnly(),
                        
                        Forms\Components\TextInput::make('payload.phone')
                            ->label('Telefon')
                            ->readOnly(),

                        Forms\Components\TextInput::make('payload.email')
                            ->label('E-Posta')
                            ->readOnly(),

                        Forms\Components\TextInput::make('payload.subject')
                            ->label('Konu')
                            ->readOnly(),
                    ])->columns(2), // İkili ızgara düzeni

                // --- 3. BÖLÜM: MESAJ İÇERİĞİ (Geniş Alan) ---
                Forms\Components\Section::make('Mesaj İçeriği')
                    ->schema([
                        // Textarea kullanarak uzun metinleri rahat okunur hale getiriyoruz
                        Forms\Components\Textarea::make('payload.message')
                            ->label('Gönderilen Mesaj')
                            ->rows(8) // Yüksekliği artırdık
                            ->readOnly()
                            ->columnSpanFull(), // Tam genişlik
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('source')
                    ->label('Kaynak')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'iletisim-sayfasi' => 'İletişim',
                        'baskan-sayfasi'   => 'Başkan',
                        'mobil-app'        => 'Mobil',
                        default            => $state,
                    })
                    ->colors([
                        'primary' => 'iletisim-sayfasi',
                        'success' => 'baskan-sayfasi',
                        'warning' => 'mobil-app',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('payload.name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->weight('bold'), // Kalın font

                Tables\Columns\TextColumn::make('payload.phone')
                    ->label('Telefon')
                    ->searchable(),
                
                // Mesajın sadece ilk 30 karakterini tabloda gösterelim
                Tables\Columns\TextColumn::make('payload.message')
                    ->label('Mesaj Özeti')
                    ->limit(30)
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()->label('Oku'),
                Tables\Actions\Action::make('print')
                    ->label('Çıktı Al')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (ContactMessage $record): string => route('admin.contact-messages.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()->label('Sil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Seçilenleri sil')
                        ->modalHeading('Seçilen mesajları sil')
                        ->modalDescription('Seçili mesajlar kalıcı olarak silinecek. Emin misiniz?')
                        ->modalSubmitActionLabel('Evet, sil'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}