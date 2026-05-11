<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * Duyuru HTML içinden “resmî duyurular listesi” gibi harici liste bağlantılarını ayırır (detay sayfasında ayrı CTA olarak gösterilir).
 */
final class AnnouncementContentCtas
{
    /**
     * @return array{html: string, externalLinks: list<array{href: string, label: string}>}
     */
    public static function pullResmiDuyurularListLinks(string $html): array
    {
        $trimmed = trim($html);
        if ($trimmed === '') {
            return ['html' => $html, 'externalLinks' => []];
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $wrapped = '<?xml encoding="UTF-8"><div id="ann-cta-root">'.$trimmed.'</div>';
        if (! @$dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD)) {
            libxml_clear_errors();

            return ['html' => $html, 'externalLinks' => []];
        }
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $anchors = $xpath->query('//a[@href]');
        if (! $anchors instanceof \DOMNodeList) {
            return ['html' => $html, 'externalLinks' => []];
        }

        $externalLinks = [];
        $toRemove = [];

        foreach ($anchors as $a) {
            if (! $a instanceof DOMElement) {
                continue;
            }
            $hrefRaw = trim($a->getAttribute('href'));
            if ($hrefRaw === '') {
                continue;
            }
            $hrefNorm = mb_strtolower($hrefRaw);
            $textNorm = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $a->textContent) ?? ''));

            $isResmiListUrl = str_contains($hrefNorm, 'resmi-duyurular')
                || str_contains($hrefNorm, 'resmî-duyurular')
                || str_contains($hrefNorm, 'resmi_duyurular');

            $isResmiListLabel = $textNorm !== '' && (
                str_contains($textNorm, 'resmî duyurular listesi')
                || str_contains($textNorm, 'resmi duyurular listesi')
                || str_contains($textNorm, 'resmi duyuru listesi')
            );

            if (! $isResmiListUrl && ! $isResmiListLabel) {
                continue;
            }

            $label = trim(preg_replace('/\s+/u', ' ', $a->textContent) ?? '');
            if ($label === '') {
                $label = 'Resmî duyurular listesi';
            }

            $externalLinks[] = [
                'href' => $hrefRaw,
                'label' => $label,
            ];
            $toRemove[] = $a;
        }

        foreach ($toRemove as $a) {
            $a->parentNode?->removeChild($a);
        }

        $root = $dom->getElementById('ann-cta-root');
        $outHtml = '';
        if ($root instanceof DOMElement) {
            foreach ($root->childNodes as $child) {
                $outHtml .= $dom->saveHTML($child) ?? '';
            }
        }

        $outHtml = preg_replace('~<p\b[^>]*>\s*(?:<br\s*/?>\s*)*</p>~iu', '', $outHtml) ?? $outHtml;
        $outHtml = trim($outHtml);

        $seen = [];
        $unique = [];
        foreach ($externalLinks as $row) {
            $key = $row['href'];
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $row;
        }

        return [
            'html' => $outHtml,
            'externalLinks' => $unique,
        ];
    }
}
