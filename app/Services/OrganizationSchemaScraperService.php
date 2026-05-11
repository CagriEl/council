<?php

namespace App\Services;

use App\Support\HtmlContentSanitizer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OrganizationSchemaScraperService
{
    /**
     * @return list<array{name: string, detail_url: string}>
     */
    public function fetchDirectorateLinks(string $url = 'https://kirklareli.bel.tr/sayfalar/66/mudurluklerimiz'): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'KirklareliOrganizationScraper/1.0',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (! $response->successful()) {
                return [];
            }

            return $this->parseDirectorateLinks($response->body(), $url);
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return array{name: string, content_html: string, image_urls: list<string>}|null
     */
    public function fetchDirectorateDetail(string $url): ?array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'KirklareliOrganizationScraper/1.0',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            return $this->parseDirectorateDetail($response->body(), $url);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return list<array{name: string, detail_url: string}>
     */
    private function parseDirectorateLinks(string $html, string $baseUrl): array
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
        $nodes = $xpath->query("//ul[contains(concat(' ', normalize-space(@class), ' '), ' side-menu__list ')]//a[contains(concat(' ', normalize-space(@class), ' '), ' side-menu__list-item__link ')]");
        if (! $nodes instanceof \DOMNodeList) {
            return [];
        }

        $items = [];
        $seen = [];
        foreach ($nodes as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }

            $name = trim(preg_replace('/\s+/u', ' ', $node->textContent ?? '') ?? '');
            $href = trim($node->getAttribute('href'));
            if ($name === '' || $href === '') {
                continue;
            }

            if (preg_match('/^\d+$/', $href) === 1) {
                $href = '/sayfalar/'.$href.'/'.Str::slug($name, '-', 'tr');
            }

            $detailUrl = $this->toAbsoluteUrl($baseUrl, $href);
            if (! str_contains($detailUrl, '/sayfalar/')) {
                continue;
            }

            if (isset($seen[$detailUrl])) {
                continue;
            }

            $items[] = [
                'name' => $name,
                'detail_url' => $detailUrl,
            ];
            $seen[$detailUrl] = true;
        }

        return $items;
    }

    /**
     * @return array{name: string, content_html: string, image_urls: list<string>}|null
     */
    private function parseDirectorateDetail(string $html, string $baseUrl): ?array
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
        $name = trim((string) $xpath->evaluate("string((//div[contains(concat(' ', normalize-space(@class), ' '), ' orientation-two ')]//a[contains(@class,'orientation-link')][last()]//p)[1])"));
        if ($name === '') {
            $name = trim((string) $xpath->evaluate('string((//h1)[1])'));
        }
        $name = trim(preg_replace('/\s+/u', ' ', $name) ?? '');
        if ($name === '') {
            return null;
        }

        $contentNode = $xpath->query("(//div[contains(concat(' ', normalize-space(@class), ' '), ' misandvis ')])[1]");
        $contentHtml = '';
        $imageUrls = [];

        if ($contentNode instanceof \DOMNodeList && $contentNode->length > 0) {
            $node = $contentNode->item(0);
            $contentHtml = trim($this->innerHtml($node));

            if ($node instanceof \DOMElement) {
                $imgs = $xpath->query('.//img[@src]', $node);
                if ($imgs instanceof \DOMNodeList) {
                    foreach ($imgs as $img) {
                        if (! $img instanceof \DOMElement) {
                            continue;
                        }
                        $src = trim($img->getAttribute('src'));
                        if ($src !== '') {
                            $imageUrls[] = $this->toAbsoluteUrl($baseUrl, $src);
                        }
                    }
                }
            }
        }

        if ($contentHtml === '') {
            $contentHtml = '<p>Detay içerik kaynaktan alınamadı.</p>';
        }

        $contentHtml = HtmlContentSanitizer::stripKaynakSayfayiAcBlocks($contentHtml);

        return [
            'name' => $name,
            'content_html' => $contentHtml,
            'image_urls' => array_values(array_unique($imageUrls)),
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
