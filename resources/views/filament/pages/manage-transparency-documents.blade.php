<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit">
                Kaydet
            </x-filament::button>
        </div>
    </form>

    <x-filament::section class="mt-8">
        <x-slot name="heading">
            Site adresi
        </x-slot>
        <p class="text-sm text-gray-600 dark:text-gray-300">
            Mali Durum raporları için:
            <a
                href="{{ route('transparency.show', 'mali-durum') }}"
                target="_blank"
                class="text-primary-600 underline"
            >
                /seffaflik-hesap-verilebilirlik/mali-durum
            </a>
        </p>
    </x-filament::section>
</x-filament-panels::page>
