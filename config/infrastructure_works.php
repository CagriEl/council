<?php

/**
 * Altyapı çalışmaları — panel (Filament) modülü gelene kadar demo kaynak.
 * Mobil: GET /api/infrastructure-works
 */
return [
    [
        'id' => 1,
        'title' => 'Merkez Mahallesi İçme Suyu Hat Yenileme',
        'summary' => 'Eski asbest hatların PE boru ile değiştirilmesi ve vanaların yenilenmesi çalışması devam ediyor. Çalışma saatleri: 08:00–17:00.',
        'location' => 'Merkez Mahallesi',
        'status' => 'ongoing',
        'status_label' => 'Devam Ediyor',
        'progress' => 65,
        'started_at' => '2026-05-12',
        'estimated_end_at' => '2026-08-30',
    ],
    [
        'id' => 2,
        'title' => 'Karacaibrahim Caddesi Asfalt Kaplama',
        'summary' => 'Yol genişletme sonrası binder ve aşınma tabakası asfalt uygulaması planlandı. Trafik tek yönlü düzenlenecektir.',
        'location' => 'Karacaibrahim Cad.',
        'status' => 'planned',
        'status_label' => 'Planlandı',
        'progress' => 10,
        'started_at' => '2026-07-01',
        'estimated_end_at' => '2026-09-15',
    ],
    [
        'id' => 3,
        'title' => 'İstasyon Mahallesi Yağmur Suyu Kanalı',
        'summary' => 'Sel riskini azaltmak için 420 metre yağmur suyu hattı döşendi. Kaldırım düzenlemesi tamamlandı.',
        'location' => 'İstasyon Mahallesi',
        'status' => 'completed',
        'status_label' => 'Tamamlandı',
        'progress' => 100,
        'started_at' => '2026-03-01',
        'estimated_end_at' => '2026-06-20',
    ],
    [
        'id' => 4,
        'title' => 'Pazaryeri Çevresi Kaldırım ve Aydınlatma',
        'summary' => 'Pazar alanı çevresinde kaldırımların yenilenmesi ve LED sokak aydınlatması montajı sürüyor.',
        'location' => 'Pazaryeri / Cumhuriyet Cad.',
        'status' => 'ongoing',
        'status_label' => 'Devam Ediyor',
        'progress' => 40,
        'started_at' => '2026-06-10',
        'estimated_end_at' => '2026-10-01',
    ],
];
