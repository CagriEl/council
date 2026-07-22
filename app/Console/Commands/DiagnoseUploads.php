<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DiagnoseUploads extends Command
{
    protected $signature = 'uploads:diagnose';

    protected $description = 'Filament/Livewire görsel yükleme sorunlarını teşhis eder';

    public function handle(): int
    {
        $this->info('APP_URL='.config('app.url'));
        $this->info('FILESYSTEM_DISK='.config('filesystems.default'));
        $this->info('Livewire tmp disk='.config('livewire.temporary_file_upload.disk'));
        $this->info('Livewire tmp dir='.config('livewire.temporary_file_upload.directory'));
        $this->newLine();

        $paths = [
            storage_path('app/public'),
            storage_path('app/public/livewire-tmp'),
            storage_path('app/public/sliders'),
            storage_path('app/private/livewire-tmp'),
            public_path('storage'),
        ];

        foreach ($paths as $path) {
            $exists = file_exists($path) ? 'var' : 'YOK';
            $writable = is_writable($path) ? 'yazılabilir' : 'YAZILAMAZ';
            $perm = file_exists($path) ? substr(sprintf('%o', fileperms($path)), -4) : '----';
            $link = is_link($path) ? ' (symlink→'.readlink($path).')' : '';
            $this->line("{$exists} | {$writable} | {$perm} | {$path}{$link}");
        }

        $this->newLine();
        $this->info('PHP: upload_max_filesize='.ini_get('upload_max_filesize').' post_max_size='.ini_get('post_max_size'));

        try {
            $ok = Storage::disk('public')->put('livewire-tmp/_diagnose.txt', 'ok');
            if ($ok) {
                Storage::disk('public')->delete('livewire-tmp/_diagnose.txt');
                $this->info('Storage::disk(public) yazma: OK');
            } else {
                $this->error('Storage::disk(public) yazma: BAŞARISIZ');
            }
        } catch (\Throwable $e) {
            $this->error('Storage yazma exception: '.$e->getMessage());
        }

        $appUrl = (string) config('app.url');
        if (! str_contains($appUrl, 'kirklareli.bel.tr') && app()->environment('production')) {
            $this->warn('Uyarı: production APP_URL kirklareli.bel.tr değil — Livewire önizleme/upload bozulabilir.');
        }

        return self::SUCCESS;
    }
}
