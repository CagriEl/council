<?php

namespace Tests\Unit;

use App\Support\AnnouncementContentCtas;
use PHPUnit\Framework\TestCase;

class AnnouncementContentCtasTest extends TestCase
{
    public function test_extracts_resmi_duyurular_href_and_strips_from_html(): void
    {
        $html = '<p>Metin.</p><p><a href="https://kirklareli.bel.tr/resmi-duyurular/2">Resmî duyurular listesi</a></p>';
        $out = AnnouncementContentCtas::pullResmiDuyurularListLinks($html);
        $this->assertCount(1, $out['externalLinks']);
        $this->assertSame('https://kirklareli.bel.tr/resmi-duyurular/2', $out['externalLinks'][0]['href']);
        $this->assertStringContainsString('Metin', $out['html']);
        $this->assertStringNotContainsString('resmi-duyurular', $out['html']);
    }

    public function test_matches_label_even_if_href_generic(): void
    {
        $html = '<p><a href="/sayfalar/66/duyurular">Resmî duyurular listesi</a></p>';
        $out = AnnouncementContentCtas::pullResmiDuyurularListLinks($html);
        $this->assertCount(1, $out['externalLinks']);
        $this->assertSame('/sayfalar/66/duyurular', $out['externalLinks'][0]['href']);
    }

    public function test_dedupes_same_href(): void
    {
        $html = '<a href="https://x.bel.tr/resmi-duyurular/1">A</a><a href="https://x.bel.tr/resmi-duyurular/1">B</a>';
        $out = AnnouncementContentCtas::pullResmiDuyurularListLinks($html);
        $this->assertCount(1, $out['externalLinks']);
    }
}
