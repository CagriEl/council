<?php

namespace Database\Seeders;

use App\Models\Directorate;
use App\Models\VicePresident;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Resmî teşkilat: başkan yardımcıları ve müdürlük eşlemesi (idempotent).
 */
class OrganisationStructureSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('vice_presidents') || ! Schema::hasTable('directorates')) {
            return;
        }

        $vicePresidents = [
            ['name' => 'Aydemir CAN', 'title' => 'Belediye Başkan Yardımcısı', 'order' => 1],
            ['name' => 'Kürşad YAMANER', 'title' => 'Belediye Başkan Yardımcısı', 'order' => 2],
            ['name' => 'Burak SÜZÜLMÜŞ', 'title' => 'Belediye Başkan Yardımcısı', 'order' => 3],
            ['name' => 'Sabri ÇINAR', 'title' => 'Belediye Başkan Yardımcısı', 'order' => 4],
            ['name' => 'Oğuz ATEŞ', 'title' => 'Belediye Başkan Yardımcısı Vekili', 'order' => 5],
        ];

        foreach ($vicePresidents as $row) {
            VicePresident::query()->updateOrCreate(
                ['name' => $row['name']],
                [
                    'title' => $row['title'],
                    'order' => $row['order'],
                ]
            );
        }

        $vpId = static fn (string $name): ?int => VicePresident::query()->where('name', $name)->value('id');

        /** @var array<string, string> Müdürlük tam adı => müdür adı soyadı */
        $managerNames = [
            'Özel Kalem Müdürlüğü' => 'Burcu KUMSAR',
            'Rehberlik ve Teftiş Kurulu Müdürlüğü' => 'Mehmet TARLACI',
            'Fen İşleri Müdürlüğü' => 'Ünal KIKILI',
            'Ulaşım Hizmetleri Müdürlüğü' => 'Osman DURAKER',
            'Makine İkmal Bakım ve Onarım Müdürlüğü' => 'Altuğ ÇAĞLAR',
            'Su ve Kanalizasyon Müdürlüğü' => 'Selçuk TANTA',
            'Bilgi İşlem Müdürlüğü' => 'Çağrı EL',
            'Zabıta Müdürlüğü' => 'Murat ÜLKÜMEN',
            'İtfaiye Müdürlüğü' => 'Öner NİZAMOĞULLARI',
            'Mezarlıklar Müdürlüğü' => 'Süleyman PERAN',
            'Afet İşleri ve Risk Yönetimi Müdürlüğü' => 'Merdol BULUT',
            'Temizlik İşleri Müdürlüğü' => 'Abdullah KELEŞ',
            'Mali Hizmetler Müdürlüğü' => 'Duygu GİDER',
            'Gelirler Müdürlüğü' => 'Hasan VURMAZ',
            'Hukuk İşleri Müdürlüğü' => 'Tansel SAİN',
            'Destek Hizmetleri Müdürlüğü' => 'Mehmet TARLACI',
            'İnsan Kaynakları ve Eğitim Müdürlüğü' => 'Engin GÜNAL',
            'Yazı İşleri Müdürlüğü' => 'Serkan ÖNAL',
            'İklim Değişikliği ve Sıfır Atık Müdürlüğü' => 'Ufuk ÖZEN',
            'Kültür Sanat ve Sosyal İşler Müdürlüğü' => 'İsmail Sarper ERCAN',
            'Veteriner İşleri Müdürlüğü' => 'Aynur GÜLHANE',
            'İmar ve Şehircilik Müdürlüğü' => 'Volkan EREN',
        ];

        $byVp = [
            'Aydemir CAN' => [
                'Fen İşleri Müdürlüğü',
                'Ulaşım Hizmetleri Müdürlüğü',
                'Makine İkmal Bakım ve Onarım Müdürlüğü',
                'Su ve Kanalizasyon Müdürlüğü',
                'Bilgi İşlem Müdürlüğü',
            ],
            'Kürşad YAMANER' => [
                'Zabıta Müdürlüğü',
                'İtfaiye Müdürlüğü',
                'Mezarlıklar Müdürlüğü',
                'Afet İşleri ve Risk Yönetimi Müdürlüğü',
                'Temizlik İşleri Müdürlüğü',
            ],
            'Burak SÜZÜLMÜŞ' => [
                'Mali Hizmetler Müdürlüğü',
                'Gelirler Müdürlüğü',
                'Hukuk İşleri Müdürlüğü',
                'Destek Hizmetleri Müdürlüğü',
            ],
            'Sabri ÇINAR' => [
                'İnsan Kaynakları ve Eğitim Müdürlüğü',
                'Yazı İşleri Müdürlüğü',
                'İklim Değişikliği ve Sıfır Atık Müdürlüğü',
                'Kültür Sanat ve Sosyal İşler Müdürlüğü',
                'Veteriner İşleri Müdürlüğü',
            ],
            'Oğuz ATEŞ' => [
                'İmar ve Şehircilik Müdürlüğü',
            ],
        ];

        foreach ($byVp as $vpName => $names) {
            $id = $vpId($vpName);
            if (! $id) {
                continue;
            }
            foreach ($names as $name) {
                $slug = Str::slug($name, '-', 'tr');
                Directorate::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'vice_president_id' => $id,
                        'name' => $name,
                        'manager_name' => $managerNames[$name] ?? null,
                    ]
                );
            }
        }

        $mayorDirectorates = [
            'Özel Kalem Müdürlüğü',
            'Rehberlik ve Teftiş Kurulu Müdürlüğü',
        ];

        foreach ($mayorDirectorates as $name) {
            $slug = Str::slug($name, '-', 'tr');
            Directorate::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'vice_president_id' => null,
                    'name' => $name,
                    'manager_name' => $managerNames[$name] ?? null,
                ]
            );
        }
    }
}
