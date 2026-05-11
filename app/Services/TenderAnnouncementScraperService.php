<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TenderAnnouncementScraperService
{
    /**
     * @return list<array{title: string, detail_url: string}>
     */
    public function fetch(string $url): array
    {
        try {
            $response = Http::timeout(25)
                ->withHeaders([
                    'User-Agent' => 'KirklareliTenderScraper/1.0',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (! $response->successful()) {
                return [];
            }

            return $this->parseHtml($response->body(), $url);
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return list<array{title: string, detail_url: string}>
     */
    private function parseHtml(string $html, string $fetchedFromUrl): array
    {
        $base = $this->originFromUrl($fetchedFromUrl);

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument;
        $wrapped = '<?xml encoding="UTF-8">'.$html;
        if (! @$dom->loadHTML($wrapped, LIBXML_NOWARNING | LIBXML_NOERROR)) {
            libxml_clear_errors();

            return [];
        }
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//li//a[@href]');
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

            $hrefLower = mb_strtolower($href);
            $looksLikeTenderLink = str_contains($hrefLower, '.pdf')
                || str_contains($hrefLower, '/files/')
                || str_contains($hrefLower, 'dokuman')
                || str_contains($hrefLower, 'ihale')
                || str_contains($hrefLower, '/duyurular/');
            if (! $looksLikeTenderLink) {
                continue;
            }

            $title = trim(preg_replace('/\s+/u', ' ', $node->textContent ?? '') ?? '');
            if ($title === '') {
                continue;
            }

            $detailUrl = $this->toAbsoluteUrl($base, $href);
            if (isset($seen[$detailUrl])) {
                continue;
            }

            $items[] = [
                'title' => $title,
                'detail_url' => $detailUrl,
            ];
            $seen[$detailUrl] = true;
        }

        return $items;
    }

    private function originFromUrl(string $url): string
    {
        $parts = parse_url($url);
        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return rtrim((string) config('app.url'), '/');
        }

        $origin = $parts['scheme'].'://'.$parts['host'];
        if (isset($parts['port'])) {
            $origin .= ':'.$parts['port'];
        }

        return $origin;
    }

    private function toAbsoluteUrl(string $originOrBase, string $pathOrUrl): string
    {
        $pathOrUrl = trim($pathOrUrl);
        if ($pathOrUrl === '') {
            return $pathOrUrl;
        }

        if (preg_match('#^https?://#i', $pathOrUrl)) {
            return $pathOrUrl;
        }

        if (str_starts_with($pathOrUrl, '//')) {
            $scheme = parse_url($originOrBase, PHP_URL_SCHEME) ?: 'https';

            return $scheme.':'.$pathOrUrl;
        }

        $baseParts = parse_url($originOrBase);
        $scheme = $baseParts['scheme'] ?? 'https';
        $host = $baseParts['host'] ?? null;
        $port = isset($baseParts['port']) ? ':'.$baseParts['port'] : '';
        $path = isset($baseParts['path']) ? trim((string) $baseParts['path']) : '';

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
