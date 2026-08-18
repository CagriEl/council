<?php

namespace App\Console\Commands;

use App\Models\CouncilMember;
use App\Models\PoliticalParty;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Meclis üyelerinin political_party_id ilişkisini party metin alanıyla senkronlar.
 * Production'da CHP üyelerinin yanlışlıkla "Diğer / Bağımsız"a bağlanması sorununu giderir.
 */
class SyncCouncilMemberParties extends Command
{
    protected $signature = 'council:sync-parties {--dry-run : Sadece raporla, kaydetme}';

    protected $description = 'Meclis üyesi parti ilişkilerini party alanına göre düzeltir';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $parties = PoliticalParty::query()->get();

        if ($parties->isEmpty()) {
            $this->error('political_parties tablosu boş.');

            return self::FAILURE;
        }

        $updated = 0;

        CouncilMember::query()->orderBy('id')->each(function (CouncilMember $member) use ($parties, $dryRun, &$updated) {
            $resolved = $this->resolveParty($member, $parties);
            if ($resolved === null) {
                $this->warn("Eşleşmedi: #{$member->id} {$member->name} (party={$member->party}, rel=".optional($member->politicalParty)->name.')');

                return;
            }

            $needsRelation = (int) $member->political_party_id !== (int) $resolved->id;
            $needsPartyString = trim((string) $member->party) !== $resolved->name;

            if (! $needsRelation && ! $needsPartyString) {
                return;
            }

            $this->line(
                ($dryRun ? '[dry-run] ' : '').
                "#{$member->id} {$member->name}: ".
                optional($member->politicalParty)->name.' → '.$resolved->name
            );

            if (! $dryRun) {
                $member->political_party_id = $resolved->id;
                $member->party = $resolved->name;
                $member->save();
            }

            $updated++;
        });

        $this->info(($dryRun ? 'Güncellenecek' : 'Güncellenen')." kayıt: {$updated}");

        return self::SUCCESS;
    }

    private function resolveParty(CouncilMember $member, $parties): ?PoliticalParty
    {
        $candidates = array_filter([
            trim((string) $member->party),
            optional($member->politicalParty)->name,
        ]);

        foreach ($candidates as $raw) {
            $normalized = $this->normalize($raw);
            if ($normalized === '' || $this->isIndependent($normalized)) {
                continue;
            }

            // 1) Tam ad / short_name eşleşmesi
            $exact = $parties->first(function (PoliticalParty $party) use ($normalized) {
                return $this->normalize($party->name) === $normalized
                    || $this->normalize((string) $party->short_name) === $normalized;
            });
            if ($exact) {
                return $exact;
            }

            // 2) Alias eşleşmesi — kısa adı tercih et (CHP > Cumhuriyet Halk Partisi)
            $aliasMatches = $parties
                ->filter(fn (PoliticalParty $party) => $this->aliasesMatch($normalized, $party))
                ->sortBy(fn (PoliticalParty $party) => mb_strlen($party->name))
                ->values();

            if ($aliasMatches->isNotEmpty()) {
                return $aliasMatches->first();
            }
        }

        // Metin bağımsız ise ilişkiyi bağımsız partisine bağla
        foreach ($candidates as $raw) {
            if ($this->isIndependent($this->normalize($raw))) {
                return $parties->first(fn (PoliticalParty $p) => $this->isIndependent($this->normalize($p->name)));
            }
        }

        return null;
    }

    private function aliasesMatch(string $normalized, PoliticalParty $party): bool
    {
        $aliases = [
            'chp' => ['chp', 'cumhuriyet halk partisi'],
            'ak parti' => ['ak parti', 'akp', 'adalet ve kalkinma partisi', 'adalet ve kalkınma partisi'],
            'mhp' => ['mhp', 'milliyetci hareket partisi', 'milliyetçi hareket partisi'],
        ];

        $partyKey = null;
        $partyNorm = $this->normalize($party->name);
        foreach ($aliases as $key => $list) {
            if (in_array($partyNorm, $list, true) || $partyNorm === $key) {
                $partyKey = $key;
                break;
            }
        }

        if ($partyKey === null) {
            return false;
        }

        return in_array($normalized, $aliases[$partyKey], true) || $normalized === $partyKey;
    }

    private function normalize(string $value): string
    {
        $value = Str::of($value)->lower()->ascii()->trim()->toString();
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return $value;
    }

    private function isIndependent(string $normalized): bool
    {
        return str_contains($normalized, 'bagimsiz')
            || str_contains($normalized, 'diger')
            || $normalized === 'independent';
    }
}
