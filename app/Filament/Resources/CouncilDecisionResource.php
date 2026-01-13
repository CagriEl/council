<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouncilDecisionResource\Pages;
use App\Models\CouncilDecision;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section; // <-- EKSİK OLAN BU SATIRDI
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;

class CouncilDecisionResource extends Resource
{
    protected static ?string $model = CouncilDecision::class;

    // Türkçe İsimlendirmeler
    protected static ?string $modelLabel = 'Meclis Kararı';
    protected static ?string $pluralModelLabel = 'Meclis Kararları';
    protected static ?string $navigationLabel = 'Meclis Kararları';
    protected static ?string $navigationGroup = 'Meclis Yönetimi';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Toplantı Bilgileri')
                    ->schema([
                        Select::make('year')
                            ->label('Meclis Yılı')
                            ->options(array_combine(range(date('Y'), 2019), range(date('Y'), 2019)))
                            ->default(date('Y'))
                            ->required(),
                        Select::make('month')
                            ->label('Meclis Ayı')
                            ->options([
                                'Ocak' => 'Ocak', 'Şubat' => 'Şubat', 'Mart' => 'Mart', 
                                'Nisan' => 'Nisan', 'Mayıs' => 'Mayıs', 'Haziran' => 'Haziran',
                                'Temmuz' => 'Temmuz', 'Ağustos' => 'Ağustos', 'Eylül' => 'Eylül',
                                'Ekim' => 'Ekim', 'Kasım' => 'Kasım', 'Aralık' => 'Aralık'
                            ])
                            ->required(),
                        TextInput::make('title')
                            ->label('Oturum Adı')
                            ->placeholder('Örn: Kasım Ayı Olağan Meclis Toplantısı')
                            ->required()
                            ->columnSpanFull(),
                        DatePicker::make('meeting_date')
                            ->label('Tam Tarih (Sıralama İçin)')
                            ->required(),
                    ])->columns(2),

                Section::make('Dokümanlar (PDF)')
                    ->description('İlgili alanlara PDF dosyalarını yükleyiniz.')
                    ->schema([
                        FileUpload::make('agenda_file')
                            ->label('Meclis Gündemi')
                            ->directory('meclis/gundem')
                            ->acceptedFileTypes(['application/pdf'])
                            ->downloadable(),
                        
                        FileUpload::make('decision_file')
                            ->label('Meclis Kararları')
                            ->directory('meclis/karar')
                            ->acceptedFileTypes(['application/pdf'])
                            ->downloadable(),

                        FileUpload::make('commission_file')
                            ->label('Komisyon Raporları')
                            ->directory('meclis/komisyon')
                            ->acceptedFileTypes(['application/pdf'])
                            ->downloadable(),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year')->label('Yıl')->sortable(),
                TextColumn::make('month')->label('Ay'),
                TextColumn::make('title')->label('Oturum Adı')->searchable(),
                TextColumn::make('meeting_date')->label('Tarih')->date('d.m.Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Yıla Göre')
                    ->options(array_combine(range(date('Y'), 2019), range(date('Y'), 2019))),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('meeting_date', 'desc');
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
            'index' => Pages\ListCouncilDecisions::route('/'),
            'create' => Pages\CreateCouncilDecision::route('/create'),
            'edit' => Pages\EditCouncilDecision::route('/{record}/edit'),
        ];
    }
}