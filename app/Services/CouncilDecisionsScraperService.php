<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CouncilDecisionsScraperService
{
    /**
     * @return list<array{title: string, file_url: string}>
     */
    public function fetchYear(int $year): array
    {
        $url = "https://kirklareli.bel.tr/kararlar/{$year}";

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'KirklareliCouncilScraper/1.0',
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
     * @return list<array{title: string, file_url: string}>
     */
    private function parseHtml(string $html, string $baseUrl): array
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
        $nodes = $xpath->query("//ul[contains(concat(' ', normalize-space(@class), ' '), ' calling__list ')]//a[@href]");
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

            $fileUrl = $this->toAbsoluteUrl($baseUrl, $href);
            $fileUrlLower = mb_strtolower($fileUrl);
            if (! str_contains($fileUrlLower, '.pdf')) {
                continue;
            }

            $title = trim(preg_replace('/\s+/u', ' ', $node->textContent ?? '') ?? '');
            if ($title === '') {
                continue;
            }

            if (isset($seen[$fileUrl])) {
                continue;
            }

            $items[] = [
                'title' => $title,
                'file_url' => $fileUrl,
            ];
            $seen[$fileUrl] = true;
        }

        return $items;
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
