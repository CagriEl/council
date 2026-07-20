<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\Transparency\TransparencySections;
use Illuminate\View\View;

class TransparencyController extends Controller
{
    private const LEGACY_SECTION_IDS = [
        '76' => 'faaliyet-raporlari',
        '25' => 'stratejik-plan',
        '89' => 'mali-durum',
        '1012' => 'ucret-tarifesi',
        '1013' => 'su-ucret-tarifesi',
        '1053' => 'performans-programi',
        '1248' => 'denetim-raporlari',
        '1345' => 'butce-kesin-hesap',
        '1509' => 'enerji-iklim-plani',
    ];

    public function show(?string $section = null): View
    {
        $sections = TransparencySections::all();

        if ($sections->isEmpty()) {
            abort(503, 'Şeffaflık içerikleri henüz yüklenmedi.');
        }

        if ($section && isset(self::LEGACY_SECTION_IDS[$section])) {
            $section = self::LEGACY_SECTION_IDS[$section];
        }

        $activeSlug = $section ?: TransparencySections::defaultSlug();
        $activeSection = TransparencySections::find($activeSlug);

        if (! $activeSection) {
            abort(404);
        }

        return view('pages.seffaflik-hesap-verilebilirlik', [
            'sections' => $sections,
            'activeSection' => $activeSection,
        ]);
    }
}
