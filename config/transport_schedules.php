<?php

/**
 * Belediye otobüs / servis sefer saatleri.
 * Panel entegrasyonu gelene kadar bu dosyadan API beslenir.
 */
return [
    [
        'id' => 'sehir',
        'label' => 'Şehir İçi',
        'color' => '#00668a',
        'weekday' => ['06:30', '07:15', '08:00', '09:00', '12:00', '17:30', '18:30', '20:00'],
        'weekend' => ['07:00', '09:00', '11:00', '14:00', '17:00', '19:00'],
        'notes' => ['08:00' => 'Yoğun Saat', '17:30' => 'Ekspres'],
    ],
    [
        'id' => 'servis1',
        'label' => 'Servis 1',
        'color' => '#476272',
        'weekday' => ['06:45', '07:30', '08:15', '12:30', '17:00', '18:00'],
        'weekend' => ['08:00', '12:00', '16:00'],
        'notes' => [],
    ],
    [
        'id' => 'servis2',
        'label' => 'Servis 2',
        'color' => '#875205',
        'weekday' => ['07:00', '08:30', '13:00', '17:15', '19:30'],
        'weekend' => ['09:30', '14:30'],
        'notes' => ['13:00' => 'Öğle'],
    ],
];
