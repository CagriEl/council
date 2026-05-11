<?php

namespace App\Support;

/**
 * Removes boilerplate links and labels often present in scraped municipal HTML.
 */
final class HtmlContentSanitizer
{
    /**
     * Strips “Kaynak sayfayı aç”, “(kaynak)” suffixes, and redundant PDF-at-source CTAs when a PDF is already attached in the CMS.
     */
    public static function stripKaynakSayfayiAcBlocks(string $html): string
    {
        if ($html === '') {
            return $html;
        }

        $out = $html;

        // Paragraph that contains only optional breaks + the anchor + optional breaks
        $out = preg_replace(
            '~<p\b[^>]*>\s*(?:<br\s*/?>\s*)*<a\b[^>]*>\s*Kaynak\s+sayfayı\s+aç\s*</a>\s*(?:<br\s*/?>\s*)*</p>~iu',
            '',
            $out
        ) ?? $out;

        // Standalone anchor (any attributes / href)
        $out = preg_replace(
            '~<a\b[^>]*>\s*Kaynak\s+sayfayı\s+aç\s*</a>~iu',
            '',
            $out
        ) ?? $out;

        // Önce “… indir (kaynak)” / “… görüntüle (kaynak)” sonekini kaldır (PDF CTA satırıyla birlikte silinmeden önce metni düzeltir)
        $out = preg_replace('~\b(?:görüntüle|indir)\s*\(\s*kaynak\s*\)~iu', '', $out) ?? $out;

        // Tek satırlık “PDF’yi kaynak sitede aç” bağlantısı / metni (panelde zaten ek dosya var)
        $pdfCta = 'PDF\s+belgesini\s+görüntüle(?:\s+veya\s+indir)?';
        $out = preg_replace(
            '~<p\b[^>]*>\s*(?:<br\s*/?>\s*)*(?:<strong\b[^>]*>)?\s*<a\b[^>]*>\s*'.$pdfCta.'\s*</a>(?:\s*</strong>)?\s*(?:<br\s*/?>\s*)?</p>~ius',
            '',
            $out
        ) ?? $out;
        $out = preg_replace(
            '~<p\b[^>]*>\s*(?:<br\s*/?>\s*)*'.$pdfCta.'\s*</p>~ius',
            '',
            $out
        ) ?? $out;
        $out = preg_replace(
            '~<a\b[^>]*>\s*'.$pdfCta.'\s*</a>~ius',
            '',
            $out
        ) ?? $out;

        // “(kaynak)” silindikten sonra kalan yarım kalıplar: “… görüntüle veya </a>”
        $pdfTail = 'PDF\s+belgesini\s+görüntüle\s+veya\s*';
        $out = preg_replace(
            '~<p\b[^>]*>\s*(?:<br\s*/?>\s*)*(?:<strong\b[^>]*>)?\s*<a\b[^>]*>\s*'.$pdfTail.'</a>(?:\s*</strong>)?\s*(?:<br\s*/?>\s*)?</p>~ius',
            '',
            $out
        ) ?? $out;
        $out = preg_replace(
            '~<a\b[^>]*>\s*'.$pdfTail.'</a>~ius',
            '',
            $out
        ) ?? $out;

        return trim($out);
    }
}
