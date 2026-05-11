<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Eski demo haber/duyuru kayıtları ve picsum ile oluşturulmuş kapak dosyalarını siler.
 */
class PurgeDemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $disk = Storage::disk('public');

        if (Schema::hasTable('news')) {
            News::query()->where('slug', 'like', 'ornek-haber-%')->delete();
        }

        if (Schema::hasTable('announcements')) {
            Announcement::query()->where('slug', 'like', 'ornek-ilan-%')->delete();
        }

        foreach (['news-covers', 'announcements/covers'] as $dir) {
            if (! $disk->exists($dir)) {
                continue;
            }
            foreach ($disk->allFiles($dir) as $path) {
                $base = basename($path);
                if (str_starts_with($base, 'ornek-haber-') || str_starts_with($base, 'ornek-ilan-')) {
                    $disk->delete($path);
                }
            }
        }
    }
}
