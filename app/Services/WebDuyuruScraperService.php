<?php

namespace App\Services;

use App\Support\HtmlContentSanitizer;
use Illuminate\Support\Facades\Http;

class WebDuyuruScraperService
{
    /**
     * @return list<array{title: string, detail_url: string, image_url: ?string}>
     */
    public function fetchList(string $url): array
    {
        try {
            $response = Http::timeout(25)
                ->withHeaders([
                    'User-Agent' => 'KirklareliWebDuyuruScraper/1.0',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (! $response->successful()) {
                return [];
            }

            return $this->parseListHtml($response->body(), $url);
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return array{title: string, content_html: string, image_url: ?string}|null
     */
    public function fetchDetail(string $url): ?array
    {
        try {
            $response = Http::timeout(25)
                ->withHeaders([
                    'User-Agent' => 'KirklareliWebDuyuruScraper/1.0',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            return $this->parseDetailHtml($response->body(), $url);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return list<array{title: string, detail_url: string, image_url: ?string}>
     */
    private function parseListHtml(string $html, string $baseUrl): array
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument;
        $wrapped = '<?xml encoding="UTF-8">'.$html;
        if (! @$dom->loadHTML($wrapped, LIBXML_NOWARNING | LIBXML_NOERROR)) {
            libxml_clear_errors();

            return [];
        }
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query("//a[contains(concat(' ', normalize-space(@class), ' '), ' callingandnews__link ')]");
        if (! $nodes instanceof \DOMNodeList) {
            return [];
        }

        $items = [];
        $seen = [];
        foreach ($nodes as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }

            $href = trim($node->getAttribute('href'));
            if ($href === '') {
                continue;
            }

            $detailUrl = $this->toAbsoluteUrl($baseUrl, $href);
            if (isset($seen[$detailUrl])) {
                continue;
            }

            $titleNode = $xpath->query(".//h3[contains(concat(' ', normalize-space(@class), ' '), ' callingandnews__heading ')]", $node);
            $title = '';
            if ($titleNode instanceof \DOMNodeList && $titleNode->length > 0) {
                $title = trim(preg_replace('/\s+/u', ' ', $titleNode->item(0)?->textContent ?? '') ?? '');
            }
            if ($title === '') {
                continue;
            }

            $imageNode = $xpath->query('.//img[@src]', $node);
            $imageUrl = null;
            if ($imageNode instanceof \DOMNodeList && $imageNode->length > 0) {
                $src = trim((string) $imageNode->item(0)?->attributes?->getNamedItem('src')?->nodeValue);
                if ($src !== '') {
                    $imageUrl = $this->toAbsoluteUrl($baseUrl, $src);
                }
            }

            $items[] = [
                'title' => $title,
                'detail_url' => $detailUrl,
                'image_url' => $imageUrl,
            ];
            $seen[$detailUrl] = true;
        }

        return $items;
    }

    /**
     * @return array{title: string, content_html: string, image_url: ?string}|null
     */
    private function parseDetailHtml(string $html, string $baseUrl): ?array
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument;
        $wrapped = '<?xml encoding="UTF-8">'.$html;
        if (! @$dom->loadHTML($wrapped, LIBXML_NOWARNING | LIBXML_NOERROR)) {
            libxml_clear_errors();

            return null;
        }
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        $title = trim(preg_replace('/\s+/u', ' ', (string) $xpath->evaluate("string(//h1[contains(concat(' ', normalize-space(@class), ' '), ' culture-fife__heading-secondary ')])")) ?? '');

        $contentHtml = '';
        $contentNode = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' culture-fife__text-box ')]//*[contains(concat(' ', normalize-space(@class), ' '), ' culture-fife__p ')]");
        if ($contentNode instanceof \DOMNodeList && $contentNode->length > 0) {
            $contentHtml = trim($this->innerHtml($contentNode->item(0)));
        }

        if ($contentHtml === '') {
            $text = trim(preg_replace('/\s+/u', ' ', (string) $xpath->evaluate("string(//div[contains(concat(' ', normalize-space(@class), ' '), ' culture-fife__text-box ')])")) ?? '');
            $contentHtml = $text !== '' ? '<p>'.e($text).'</p>' : '<p> </p>';
        }

        $imageUrl = null;
        $imgNode = $xpath->query("(//div[contains(concat(' ', normalize-space(@class), ' '), ' culture-fife__content-box ')]//img[@src])[1]");
        if ($imgNode instanceof \DOMNodeList && $imgNode->length > 0) {
            $src = trim((string) $imgNode->item(0)?->attributes?->getNamedItem('src')?->nodeValue);
            if ($src !== '') {
                $imageUrl = $this->toAbsoluteUrl($baseUrl, $src);
            }
        }

        return [
            'title' => $title,
            'content_html' => HtmlContentSanitizer::stripKaynakSayfayiAcBlocks($contentHtml),
            'image_url' => $imageUrl,
        ];
    }

    private function innerHtml(?\DOMNode $node): string
    {
        if (! $node || ! $node->hasChildNodes()) {
            return '';
        }

        $html = '';
        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument?->saveHTML($child) ?? '';
        }

        return $html;
    }

    private function toAbsoluteUrl(string $baseUrl, string $pathOrUrl): string
    {
        $pathOrUrl = trim($pathOrUrl);
        if ($pathOrUrl === '') {
            return $pathOrUrl;
        }

        if (preg_match('#^https?://#i', $pathOrUrl)) {
            return $pathOrUrl;
        }

        if (str_starts_with($pathOrUrl, '//')) {
            $scheme = parse_url($baseUrl, PHP_URL_SCHEME) ?: 'https';

            return $scheme.':'.$pathOrUrl;
        }

        $parts = parse_url($baseUrl);
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? null;
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $path = isset($parts['path']) ? trim((string) $parts['path']) : '';

        if (! $host) {
            return $pathOrUrl;
        }

        $origin = $scheme.'://'.$host.$port;
        if (str_starts_with($pathOrUrl, '/')) {
            return $origin.$pathOrUrl;
        }

        $basePath = $path !== '' ? rtrim(dirname($path), '/') : '';
        if ($basePath === '.' || $basePath === DIRECTORY_SEPARATOR) {
            $basePath = '';
        }

        return $origin.($basePath !== '' ? $basePath.'/' : '/').ltrim($pathOrUrl, '/');
    }
}
