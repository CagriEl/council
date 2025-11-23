<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouncilMeetingResource\Pages;
use App\Filament\Resources\CouncilMeetingResource\RelationManagers;
use App\Models\CouncilMeeting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;


class CouncilMeetingResource extends Resource
{
    protected static ?string $model = CouncilMeeting::class;

protected static ?string $modelLabel = 'Siyasi Parti';
protected static ?string $pluralModelLabel = 'Siyasi Partiler';
protected static ?string $navigationLabel = 'Parti Tanımları';
protected static ?string $navigationGroup = 'Meclis Yönetimi';
protected static ?string $navigationIcon = 'heroicon-o-flag';
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Toplantı Bilgileri')
                ->schema([
                    Select::make('year')
                        ->options(array_combine(range(date('Y'), 2020), range(date('Y'), 2020)))
                        ->label('Yıl')
                        ->required(),
                    Select::make('month')
                        ->options([
                            'Ocak'=>'Ocak', 'Şubat'=>'Şubat', 'Mart'=>'Mart', 'Nisan'=>'Nisan',
                            'Mayıs'=>'Mayıs', 'Haziran'=>'Haziran', 'Temmuz'=>'Temmuz', 'Ağustos'=>'Ağustos',
                            'Eylül'=>'Eylül', 'Ekim'=>'Ekim', 'Kasım'=>'Kasım', 'Aralık'=>'Aralık'
                        ])
                        ->label('Ay')
                        ->required(),
                    TextInput::make('title')->label('Başlık')->required()->columnSpanFull(),
                    DatePicker::make('meeting_date')->label('Tarih'),
                ])->columns(2),

            Section::make('Kararlar ve Dosyalar')
                ->schema([
                    Repeater::make('documents') // JSON Sütunu
                        ->label('Dosyalar')
                        ->schema([
                            TextInput::make('name')->label('Dosya Adı (Örn: Gündem)')->required(),
                            FileUpload::make('file')->label('PDF Dosyası')->directory('council-docs')->acceptedFileTypes(['application/pdf']),
                        ])
                        ->addActionLabel('Yeni Dosya Ekle')
                ])
        ]);
}
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCouncilMeetings::route('/'),
            'create' => Pages\CreateCouncilMeeting::route('/create'),
            'edit' => Pages\EditCouncilMeeting::route('/{record}/edit'),
        ];
    }
}
