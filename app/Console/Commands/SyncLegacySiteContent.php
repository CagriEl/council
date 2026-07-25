<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLegacySiteContent extends Command
{
    protected $signature = 'import:sync-legacy-site
        {--year=2026 : Meclis yılı}
        {--skip-meclis : Meclis import atla}
        {--skip-pages : CMS sayfa import atla}
        {--skip-seffaflik : Şeffaflık import atla}
        {--skip-duyurular : Duyuru import atla}
        {--skip-mirror : API mirror atla}';

    protected $description = 'Canlı eski siteden eksik içerikleri çeker: CMS sayfalar, duyurular, meclis evrakları, şeffaflık, API dosya mirror';

    public function handle(): int
    {
        if (! $this->option('skip-pages')) {
            $this->info('=== CMS / Kurumsal sayfalar ===');
            $this->call('import:kurumsal-mevzuat');
        }

        if (! $this->option('skip-duyurular')) {
            $this->info('=== Duyurular (genel / ihale / resmi) ===');
            $this->call('import:duyurular');
        }

        if (! $this->option('skip-seffaflik')) {
            $this->info('=== Şeffaflık ===');
            $this->call('import:seffaflik');
        }

        if (! $this->option('skip-meclis')) {
            $this->info('=== Meclis (resmi duyurular) ===');
            $this->call('import:meclis-resmi', [
                '--year' => (int) $this->option('year'),
                '--pages' => 2,
            ]);
        }

        if (! $this->option('skip-mirror')) {
            $this->info('=== Legacy API mirror ===');
            $this->call('import:mirror-legacy-api');
        }

        $this->info('Senkron tamamlandı.');

        return self::SUCCESS;
    }
}
