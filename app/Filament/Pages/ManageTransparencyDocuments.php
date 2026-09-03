<?php

namespace App\Filament\Pages;

use App\Support\Transparency\TransparencySections;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class ManageTransparencyDocuments extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'Şeffaflık Belgeleri';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?int $navigationSort = 25;

    protected static ?string $title = 'Şeffaflık ve Hesap Verilebilirlik';

    protected static string $view = 'filament.pages.manage-transparency-documents';

    protected static ?string $slug = 'seffaflik-belgeleri';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $sections = TransparencySections::all()
            ->map(function (array $section) {
                $documents = collect($section['documents'] ?? [])
                    ->map(fn (array $document) => [
                        'title' => $document['title'] ?? '',
                        'file_path' => $document['file_path'] ?? null,
                        'external_url' => empty($document['file_path']) ? ($document['url'] ?? null) : null,
                    ])
                    ->values()
                    ->all();

                return [
                    'slug' => $section['slug'] ?? '',
                    'title' => $section['title'] ?? '',
                    'documents' => $documents,
                ];
            })
            ->values()
            ->all();

        $this->form->fill([
            'sections' => $sections,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bölümler')
                    ->description('Örn. Mali Durum Beklentiler Raporu. Her bölümün altına PDF ekleyebilirsiniz.')
                    ->schema([
                        Forms\Components\Repeater::make('sections')
                            ->label('Şeffaflık bölümleri')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Bölüm başlığı')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->label('URL anahtarı')
                                    ->helperText('Örn: mali-durum — değiştirirseniz eski bağlantılar kırılabilir.')
                                    ->required()
                                    ->maxLength(100)
                                    ->regex('/^[a-z0-9\-]+$/'),
                                Forms\Components\Repeater::make('documents')
                                    ->label('Belgeler / raporlar')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Belge başlığı')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Forms\Components\FileUpload::make('file_path')
                                            ->label('PDF dosyası')
                                            ->disk('public')
                                            ->directory('transparency')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->helperText('Yeni rapor için PDF yükleyin.')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('external_url')
                                            ->label('Harici bağlantı (isteğe bağlı)')
                                            ->url()
                                            ->helperText('PDF yoksa dış URL kullanılır.')
                                            ->columnSpanFull(),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Belge ekle')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Yeni belge')
                                    ->columnSpanFull(),
                            ])
                            ->orderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Yeni bölüm')
                            ->addActionLabel('Bölüm ekle')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $sections = [];

        foreach ($state['sections'] ?? [] as $section) {
            $documents = [];

            foreach ($section['documents'] ?? [] as $document) {
                $filePath = $document['file_path'] ?? null;
                if (is_array($filePath)) {
                    $filePath = $filePath[0] ?? null;
                }

                $filePath = is_string($filePath) && $filePath !== '' ? ltrim($filePath, '/') : null;
                $externalUrl = is_string($document['external_url'] ?? null) ? trim($document['external_url']) : null;

                if (! $filePath && ! $externalUrl) {
                    continue;
                }

                $url = $filePath
                    ? Storage::disk('public')->url($filePath)
                    : $externalUrl;

                $documents[] = array_filter([
                    'title' => trim((string) ($document['title'] ?? 'Belge')),
                    'file_path' => $filePath,
                    'url' => $url,
                ], fn ($value) => $value !== null && $value !== '');
            }

            $sections[] = [
                'slug' => trim((string) $section['slug']),
                'title' => trim((string) $section['title']),
                'documents' => $documents,
            ];
        }

        TransparencySections::save($sections);

        Notification::make()
            ->title('Şeffaflık belgeleri kaydedildi')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Kaydet')
                ->submit('save'),
        ];
    }
}
