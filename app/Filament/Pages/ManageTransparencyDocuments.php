<?php

namespace App\Filament\Pages;

use App\Support\Transparency\TransparencySections;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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

    protected static ?string $title = 'Şeffaflık Belgeleri';

    protected static string $view = 'filament.pages.manage-transparency-documents';

    protected static ?string $slug = 'seffaflik-belgeleri';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $defaultSlug = TransparencySections::all()->first()['slug']
            ?? TransparencySections::defaultSlug();

        $this->form->fill([
            'section_slug' => $defaultSlug,
            'documents' => $this->documentsForSlug($defaultSlug),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bölüm seçin')
                    ->description('Önce hangi gruba belge ekleyeceğinizi seçin. Sadece o grubun belgeleri listelenir.')
                    ->schema([
                        Forms\Components\Select::make('section_slug')
                            ->label('Başlık / grup')
                            ->options(fn (): array => TransparencySections::all()
                                ->mapWithKeys(fn (array $section) => [
                                    ($section['slug'] ?? '') => $section['title'] ?? ($section['slug'] ?? ''),
                                ])
                                ->filter(fn ($title, $slug) => $slug !== '')
                                ->all())
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (?string $state, Forms\Set $set): void {
                                $set('documents', $this->documentsForSlug($state));
                            }),
                        Forms\Components\Placeholder::make('section_url')
                            ->label('Sitede görüneceği adres')
                            ->content(function (Get $get): string {
                                $slug = $get('section_slug');
                                if (! $slug) {
                                    return '—';
                                }

                                return url('/seffaflik-hesap-verilebilirlik/'.$slug);
                            }),
                    ]),

                Forms\Components\Section::make('Belgeler')
                    ->description('Seçili gruba ait raporları buradan ekleyin, silin veya sıralayın.')
                    ->schema([
                        Forms\Components\Repeater::make('documents')
                            ->label('Belgeler')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Belge başlığı')
                                    ->placeholder('Örn: 2026 YILI MALİ DURUM BEKLENTİLER RAPORU')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                Forms\Components\FileUpload::make('file_path')
                                    ->label('PDF')
                                    ->disk('public')
                                    ->directory('transparency')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->required(fn (Get $get): bool => blank($get('external_url')))
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('external_url')
                                    ->label('Harici URL (PDF yoksa)')
                                    ->url()
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Belge ekle')
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => filled($state['title'] ?? null) ? $state['title'] : 'Yeni belge')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $slug = trim((string) ($state['section_slug'] ?? ''));

        if ($slug === '') {
            Notification::make()
                ->title('Lütfen bir bölüm seçin')
                ->danger()
                ->send();

            return;
        }

        $documents = [];
        foreach ($state['documents'] ?? [] as $document) {
            $filePath = $document['file_path'] ?? null;
            if (is_array($filePath)) {
                $filePath = $filePath[0] ?? null;
            }

            $filePath = is_string($filePath) && $filePath !== '' ? ltrim($filePath, '/') : null;
            $externalUrl = is_string($document['external_url'] ?? null) ? trim((string) $document['external_url']) : null;

            if (! $filePath && ! $externalUrl) {
                continue;
            }

            $documents[] = array_filter([
                'title' => trim((string) ($document['title'] ?? 'Belge')),
                'file_path' => $filePath,
                'url' => $filePath ? Storage::disk('public')->url($filePath) : $externalUrl,
            ], fn ($value) => $value !== null && $value !== '');
        }

        $sections = TransparencySections::all()
            ->map(function (array $section) use ($slug, $documents) {
                if (($section['slug'] ?? '') !== $slug) {
                    return $section;
                }

                $section['documents'] = $documents;

                return $section;
            })
            ->values()
            ->all();

        TransparencySections::save($sections);

        $title = TransparencySections::find($slug)['title'] ?? $slug;

        Notification::make()
            ->title("“{$title}” belgeleri kaydedildi")
            ->success()
            ->send();
    }

    /**
     * @return list<array{title: string, file_path: ?string, external_url: ?string}>
     */
    private function documentsForSlug(?string $slug): array
    {
        if (! $slug) {
            return [];
        }

        $section = TransparencySections::find($slug);
        if (! $section) {
            return [];
        }

        return collect($section['documents'] ?? [])
            ->map(fn (array $document) => [
                'title' => $document['title'] ?? '',
                'file_path' => $document['file_path'] ?? null,
                'external_url' => empty($document['file_path']) ? ($document['url'] ?? null) : null,
            ])
            ->values()
            ->all();
    }
}
