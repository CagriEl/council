<?php

namespace Tests\Unit;

use App\Support\HtmlContentSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlContentSanitizerTest extends TestCase
{
    public function test_strips_paragraph_wrapped_anchor(): void
    {
        $html = '<p>Merhaba</p><p><a href="https://example.com/x">Kaynak sayfayı aç</a></p><p>Son</p>';
        $out = HtmlContentSanitizer::stripKaynakSayfayiAcBlocks($html);
        $this->assertStringContainsString('Merhaba', $out);
        $this->assertStringContainsString('Son', $out);
        $this->assertStringNotContainsString('Kaynak sayfayı aç', $out);
        $this->assertStringNotContainsString('example.com', $out);
    }

    public function test_strips_standalone_anchor(): void
    {
        $html = '<div><a class="x" href="https://kirklareli.bel.tr/a">Kaynak sayfayı aç</a></div>';
        $out = HtmlContentSanitizer::stripKaynakSayfayiAcBlocks($html);
        $this->assertSame('<div></div>', $out);
    }

    public function test_preserves_unrelated_links(): void
    {
        $html = '<p><a href="/b">Daha fazla bilgi</a></p>';
        $this->assertSame($html, HtmlContentSanitizer::stripKaynakSayfayiAcBlocks($html));
    }

    public function test_strips_pdf_source_cta_paragraph(): void
    {
        $html = '<p>İlan metni.</p><p><a href="https://kirklareli.bel.tr/files/x.pdf">PDF belgesini görüntüle veya indir (kaynak)</a></p>';
        $out = HtmlContentSanitizer::stripKaynakSayfayiAcBlocks($html);
        $this->assertStringContainsString('İlan metni', $out);
        $this->assertStringNotContainsString('kirklareli.bel.tr/files', $out);
        $this->assertStringNotContainsString('(kaynak)', $out);
    }

    public function test_preserves_unrelated_kaynak_word_in_parentheses(): void
    {
        $html = '<p>Alıntı (Kaynak: gazete)</p>';
        $this->assertSame($html, HtmlContentSanitizer::stripKaynakSayfayiAcBlocks($html));
    }
}
