<?php

namespace App\Console\Commands;

use App\Models\ContactMessage;
use App\Services\ContactSpamGuard;
use Illuminate\Console\Command;

class PurgeContactSpam extends Command
{
    protected $signature = 'contact:purge-spam {--dry-run : Sadece say, silme}';

    protected $description = 'İletişim formundaki bilinen spam mesajları (ör. rate test) temizler';

    public function handle(ContactSpamGuard $guard): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $deleted = 0;
        $matched = 0;

        ContactMessage::query()->orderBy('id')->chunkById(200, function ($messages) use ($guard, $dryRun, &$deleted, &$matched) {
            foreach ($messages as $message) {
                if (! $guard->payloadLooksLikeSpam($message->payload)) {
                    continue;
                }

                $matched++;

                if ($dryRun) {
                    $this->line("#{$message->id} spam adayı");
                    continue;
                }

                $message->delete();
                $deleted++;
            }
        });

        if ($dryRun) {
            $this->info("Bulunan spam adayı: {$matched}");
        } else {
            $this->info("Silinen spam mesaj: {$deleted}");
        }

        return self::SUCCESS;
    }
}
