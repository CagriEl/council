<?php

namespace App\Support\Transparency;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class TransparencySections
{
    private const STORAGE_PATH = 'transparency-sections.json';

    public static function all(): Collection
    {
        if (! Storage::disk('local')->exists(self::STORAGE_PATH)) {
            return collect();
        }

        $data = json_decode(Storage::disk('local')->get(self::STORAGE_PATH), true);

        return collect($data['sections'] ?? []);
    }

    public static function find(string $slug): ?array
    {
        return self::all()->firstWhere('slug', $slug);
    }

    public static function defaultSlug(): string
    {
        return self::all()->first()['slug'] ?? 'faaliyet-raporlari';
    }

    public static function save(array $sections): void
    {
        Storage::disk('local')->put(
            self::STORAGE_PATH,
            json_encode(['sections' => $sections], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }
}
