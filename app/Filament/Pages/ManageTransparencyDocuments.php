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

    protected static ?string $title = 'Şeffaflık Belgeleri';

    protected static string $view = 'filament.pages.manage-transparency-documents';

    protected static ?string $slug = 'seffaflik-belgeleri';

    public ?string $sectionSlug = null;

    /**
     * @var list<array{title: string, file_path: ?string, url: ?string}>
     */
    public array $documents = [];

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->sectionSlug = TransparencySections::all()->first()['slug']
            ?? TransparencySections::defaultSlug();

        $this->loadDocuments();
        $this->form->fill([
            'new_title' => null,
            'new_file' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Yeni belge ekle')
                    ->description('Seçili gruba PDF eklemek için doldurun.')
                    ->schema([
                        Forms\Components\TextInput::make('new_title')
                            ->label('Belge başlığı')
                            ->placeholder('Örn: 2026 YILI MALİ DURUM BEKLENTİLER RAPORU')
                            ->maxLength(255)
                            ->requiredWith('new_file'),
                        Forms\Components\FileUpload::make('new_file')
                            ->label('PDF dosyası')
                            ->disk('public')
                            ->directory('transparency')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->requiredWith('new_title'),
                    ]),
            ])
            ->statePath('data');
    }

    public function updatedSectionSlug(): void
    {
        $this->loadDocuments();
        $this->form->fill([
            'new_title' => null,
            'new_file' => null,
        ]);
    }

    public function addDocument(): void
    {
        $state = $this->form->getState();
        $title = trim((string) ($state['new_title'] ?? ''));
        $file = $state['new_file'] ?? null;

        if (is_array($file)) {
            $file = $file[0] ?? null;
        }

        $file = is_string($file) && $file !== '' ? ltrim($file, '/') : null;

        if ($title === '' || ! $file) {
            Notification::make()
                ->title('Başlık ve PDF zorunludur')
                ->danger()
                ->send();

            return;
        }

        $this->documents[] = [
            'title' => $title,
            'file_path' => $file,
            'url' => Storage::disk('public')->url($file),
        ];

        $this->persistDocuments();

        $this->form->fill([
            'new_title' => null,
            'new_file' => null,
        ]);

        Notification::make()
            ->title('Belge eklendi')
            ->success()
            ->send();
    }

    public function removeDocument(int $index): void
    {
        if (! array_key_exists($index, $this->documents)) {
            return;
        }

        unset($this->documents[$index]);
        $this->documents = array_values($this->documents);
        $this->persistDocuments();

        Notification::make()
            ->title('Belge silindi')
            ->success()
            ->send();
    }

    public function moveDocumentUp(int $index): void
    {
        if ($index <= 0 || ! array_key_exists($index, $this->documents)) {
            return;
        }

        [$this->documents[$index - 1], $this->documents[$index]] = [$this->documents[$index], $this->documents[$index - 1]];
        $this->persistDocuments();
    }

    public function moveDocumentDown(int $index): void
    {
        if (! array_key_exists($index, $this->documents) || ! array_key_exists($index + 1, $this->documents)) {
            return;
        }

        [$this->documents[$index + 1], $this->documents[$index]] = [$this->documents[$index], $this->documents[$index + 1]];
        $this->persistDocuments();
    }

    /**
     * @return array<string, string>
     */
    public function getSectionOptionsProperty(): array
    {
        return TransparencySections::all()
            ->mapWithKeys(fn (array $section) => [
                (string) ($section['slug'] ?? '') => (string) ($section['title'] ?? $section['slug'] ?? ''),
            ])
            ->filter(fn (string $title, string $slug) => $slug !== '')
            ->all();
    }

    public function getSelectedSectionTitleProperty(): string
    {
        if (! $this->sectionSlug) {
            return '';
        }

        return (string) (TransparencySections::find($this->sectionSlug)['title'] ?? $this->sectionSlug);
    }

    public function getSelectedSectionUrlProperty(): string
    {
        if (! $this->sectionSlug) {
            return '';
        }

        return url('/seffaflik-hesap-verilebilirlik/'.$this->sectionSlug);
    }

    public function documentPublicUrl(array $document): string
    {
        if (! empty($document['file_path'])) {
            return asset('storage/'.ltrim((string) $document['file_path'], '/'));
        }

        return (string) ($document['url'] ?? '#');
    }

    private function loadDocuments(): void
    {
        if (! $this->sectionSlug) {
            $this->documents = [];

            return;
        }

        $section = TransparencySections::find($this->sectionSlug);
        $this->documents = collect($section['documents'] ?? [])
            ->map(fn ($document) => [
                'title' => (string) ($document['title'] ?? 'Belge'),
                'file_path' => $document['file_path'] ?? null,
                'url' => $document['url'] ?? null,
            ])
            ->values()
            ->all();
    }

    private function persistDocuments(): void
    {
        $slug = trim((string) $this->sectionSlug);
        if ($slug === '') {
            return;
        }

        $documents = collect($this->documents)
            ->map(function (array $document) {
                $filePath = is_string($document['file_path'] ?? null) && $document['file_path'] !== ''
                    ? ltrim($document['file_path'], '/')
                    : null;
                $url = $filePath
                    ? Storage::disk('public')->url($filePath)
                    : ($document['url'] ?? null);

                if (! $filePath && ! $url) {
                    return null;
                }

                return array_filter([
                    'title' => trim((string) ($document['title'] ?? 'Belge')) ?: 'Belge',
                    'file_path' => $filePath,
                    'url' => $url,
                ], fn ($value) => $value !== null && $value !== '');
            })
            ->filter()
            ->values()
            ->all();

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
        $this->loadDocuments();
    }
}
