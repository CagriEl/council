<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityReportResource\Pages;
use App\Models\ActivityReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ActivityReportResource extends Resource
{
    protected static ?string $model = ActivityReport::class;

    protected static ?string $modelLabel = 'Faaliyet Raporu';

    protected static ?string $pluralModelLabel = 'Faaliyet Raporları';

    protected static ?string $navigationLabel = 'Faaliyet Raporları';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Rapor Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('year')
                            ->label('Yıl')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Rapor Dosyası (PDF)')
                            ->directory('activity-reports')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),
                        Forms\Components\TextInput::make('source_url')
                            ->label('Kaynak URL')
                            ->url()
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->limit(70),
                Tables\Columns\TextColumn::make('year')
                    ->label('Yıl')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Eklenme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktiflik'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (ActivityReport $record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
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
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityReports::route('/'),
            'create' => Pages\CreateActivityReport::route('/create'),
            'edit' => Pages\EditActivityReport::route('/{record}/edit'),
        ];
    }
}
