<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Bölüm seçin</x-slot>
            <x-slot name="description">Belge eklemek istediğiniz başlığı seçin.</x-slot>

            <div class="space-y-3">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Başlık / grup</label>
                <select
                    wire:model.live="sectionSlug"
                    class="fi-input block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                >
                    @foreach($this->sectionOptions as $slug => $title)
                        <option value="{{ $slug }}">{{ $title }}</option>
                    @endforeach
                </select>

                @if($this->selectedSectionUrl)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Site:
                        <a href="{{ $this->selectedSectionUrl }}" target="_blank" class="text-primary-600 underline">
                            {{ $this->selectedSectionUrl }}
                        </a>
                    </p>
                @endif
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                {{ $this->selectedSectionTitle ?: 'Belgeler' }}
            </x-slot>
            <x-slot name="description">
                Bu gruptaki mevcut belgeler
            </x-slot>

            @if(count($documents) === 0)
                <p class="text-sm text-gray-500 dark:text-gray-400">Bu bölümde henüz belge yok.</p>
            @else
                <ul class="divide-y divide-gray-200 rounded-xl border border-gray-200 dark:divide-gray-700 dark:border-gray-700">
                    @foreach($documents as $index => $document)
                        <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $document['title'] }}
                                </div>
                                <a
                                    href="{{ $this->documentPublicUrl($document) }}"
                                    target="_blank"
                                    class="text-sm text-primary-600 underline"
                                >
                                    PDF’yi aç
                                </a>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <x-filament::button
                                    color="gray"
                                    size="sm"
                                    wire:click="moveDocumentUp({{ $index }})"
                                    :disabled="$index === 0"
                                >
                                    Yukarı
                                </x-filament::button>
                                <x-filament::button
                                    color="gray"
                                    size="sm"
                                    wire:click="moveDocumentDown({{ $index }})"
                                    :disabled="$index === count($documents) - 1"
                                >
                                    Aşağı
                                </x-filament::button>
                                <x-filament::button
                                    color="danger"
                                    size="sm"
                                    wire:click="removeDocument({{ $index }})"
                                    wire:confirm="Bu belgeyi silmek istediğinize emin misiniz?"
                                >
                                    Sil
                                </x-filament::button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-filament::section>

        <form wire:submit="addDocument" class="space-y-4">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-filament::button type="submit">
                    Seçili gruba belge ekle
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
