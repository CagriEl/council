<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StrategicPlanResource\Pages;
use App\Models\StrategicPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class StrategicPlanResource extends Resource
{
    protected static ?string $model = StrategicPlan::class;

    protected static ?string $modelLabel = 'Stratejik Plan';

    protected static ?string $pluralModelLabel = 'Stratejik Planlar';

    protected static ?string $navigationLabel = 'Stratejik Planlar';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan Bilgileri')
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
                            ->label('Plan Dosyası (PDF)')
                            ->directory('strategic-plans')
                            ->acceptedFileTypes(['application/pdf']),
                        Forms\Components\TextInput::make('source_url')
                            ->label('Kaynak URL')
                            ->url(),
                        Forms\Components\Textarea::make('note')
                            ->label('Not')
                            ->rows(3)
                            ->columnSpanFull(),
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
                Tables\Columns\IconColumn::make('file_path')
                    ->label('Dosya')
                    ->boolean()
                    ->getStateUsing(fn (StrategicPlan $record) => filled($record->file_path)),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktiflik'),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->visible(fn (StrategicPlan $record) => filled($record->file_path))
                    ->url(fn (StrategicPlan $record) => asset('storage/' . $record->file_path))
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
            'index' => Pages\ListStrategicPlans::route('/'),
            'create' => Pages\CreateStrategicPlan::route('/create'),
            'edit' => Pages\EditStrategicPlan::route('/{record}/edit'),
        ];
    }
}
