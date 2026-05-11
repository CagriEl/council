<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AnnouncementScraperService
{
    /**
     * @return list<array{title: string, image_url: ?string, detail_url: string}>
     */
    public function fetchOfficialList(?string $url = null): array
    {
        $url ??= $this->defaultScraperUrl();

        try {
            $response = Http::timeout(25)
                ->withHeaders([
                    'User-Agent' => 'KirklareliAnnouncementScraper/1.0',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (! $response->successful()) {
                return [];
            }

            return $this->parseAnnouncementListHtml($response->body(), $url);
        } catch (\Throwable) {
            return [];
        }
    }

    public function defaultScraperUrl(): string
    {
        $configured = config('announcements.scraper_url');
        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        return 'https://kirklareli.bel.tr/resmi-duyurular/2';
    }

    /**
     * @return list<array{title: string, image_url: ?string, detail_url: string}>
     */
    protected function parseAnnouncementListHtml(string $html, string $fetchedFromUrl): array
    {
        $baseForLinks = $this->originFromUrl($fetchedFromUrl);

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument;
        $wrapped = '<?xml encoding="UTF-8">'.$html;
        if (! @$dom->loadHTML($wrapped, LIBXML_NOWARNING | LIBXML_NOERROR)) {
            libxml_clear_errors();

            return [];
        }
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // kirklareli.bel.tr — Resmî Duyurular (PDF bağlantıları, calling__ şablonu)
        $callingLinks = $xpath->query("//ul[contains(concat(' ', normalize-space(@class), ' '), ' calling__list ')]//a[contains(concat(' ', normalize-space(@class), ' '), ' calling__link ')][@href]");
        if ($callingLinks instanceof \DOMNodeList && $callingLinks->length > 0) {
            return $this->parseCallingListLinks($callingLinks, $xpath, $baseForLinks);
        }

        $nodes = $xpath->query("//a[contains(concat(' ', normalize-space(@class), ' '), ' ann-list-card ')]");

        if (! $nodes instanceof \DOMNodeList) {
            return [];
        }

        $items = [];
        $seen = [];

        foreach ($nodes as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }

            $href = $node->getAttribute('href');
            if ($href === '') {
                continue;
            }

            $detailUrl = $this->toAbsoluteUrl($baseForLinks, $href);

            $title = '';
            $titleNodes = $xpath->query(".//div[contains(concat(' ', normalize-space(@class), ' '), ' title ')]", $node);
            if ($titleNodes instanceof \DOMNodeList && $titleNodes->length > 0) {
                $title = trim($titleNodes->item(0)?->textContent ?? '');
            }

            $imageUrl = null;
            $imgNodes = $xpath->query('.//div[contains(@class, "img-wrap")]//img[@src]', $node);
            if ($imgNodes instanceof \DOMNodeList) {
                foreach ($imgNodes as $img) {
                    if (! $img instanceof \DOMElement) {
                        continue;
                    }
                    $src = trim($img->getAttribute('src'));
                    if ($src !== '') {
                        $imageUrl = $this->toAbsoluteUrl($baseForLinks, $src);
                        break;
                    }
                }
            }

            $record = [
                'title' => $title !== '' ? $title : 'Duyuru',
                'image_url' => $imageUrl,
                'detail_url' => $detailUrl,
            ];
            if (! isset($seen[$record['detail_url']])) {
                $items[] = $record;
                $seen[$record['detail_url']] = true;
            }
        }

        // Yeni kirklareli.bel.tr şablonu çoğunlukla liste bağlantıları (li > a) yayımlıyor.
        if ($items === []) {
            $linkNodes = $xpath->query('//li//a[@href]');
            if ($linkNodes instanceof \DOMNodeList) {
                foreach ($linkNodes as $linkNode) {
                    if (! $linkNode instanceof \DOMElement) {
                        continue;
                    }

                    $href = trim($linkNode->getAttribute('href'));
                    if ($href === '') {
                        continue;
                    }
                    $hrefLower = mb_strtolower($href);
                    $looksLikeAnnouncementLink = str_contains($hrefLower, '.pdf')
                        || str_contains($hrefLower, '/files/')
                        || str_contains($hrefLower, 'dokuman')
                        || str_contains($hrefLower, '/duyurular/');
                    if (! $looksLikeAnnouncementLink) {
                        continue;
                    }

                    $title = trim(preg_replace('/\s+/u', ' ', $linkNode->textContent ?? '') ?? '');
                    if ($title === '') {
                        continue;
                    }

                    $detailUrl = $this->toAbsoluteUrl($baseForLinks, $href);
                    if (isset($seen[$detailUrl])) {
                        continue;
                    }

                    $items[] = [
                        'title' => $title,
                        'image_url' => null,
                        'detail_url' => $detailUrl,
                    ];
                    $seen[$detailUrl] = true;
                }
            }
        }

        return $items;
    }

    /**
     * @return list<array{title: string, image_url: ?string, detail_url: string}>
     */
    protected function parseCallingListLinks(\DOMNodeList $callingLinks, \DOMXPath $xpath, string $baseForLinks): array
    {
        $items = [];
        $seen = [];

        foreach ($callingLinks as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }

            $href = trim($node->getAttribute('href'));
            if ($href === '') {
                continue;
            }

            $detailUrl = $this->toAbsoluteUrl($baseForLinks, $href);
            if (isset($seen[$detailUrl])) {
                continue;
            }

            $title = '';
            $spanNodes = $xpath->query('.//span', $node);
            if ($spanNodes instanceof \DOMNodeList && $spanNodes->length > 0) {
                $title = trim(preg_replace('/\s+/u', ' ', $spanNodes->item(0)?->textContent ?? '') ?? '');
            }
            if ($title === '') {
                $title = trim(preg_replace('/\s+/u', ' ', $node->textContent ?? '') ?? '');
            }
            if ($title === '') {
                $title = 'Resmî duyuru';
            }

            $items[] = [
                'title' => $title,
                'image_url' => null,
                'detail_url' => $detailUrl,
            ];
            $seen[$detailUrl] = true;
        }

        return $items;
    }

    protected function originFromUrl(string $url): string
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

    protected function toAbsoluteUrl(string $originOrBase, string $pathOrUrl): string
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
            $fallback = parse_url((string) config('app.url'));
            $scheme = $fallback['scheme'] ?? $scheme;
            $host = $fallback['host'] ?? null;
            $port = isset($fallback['port']) ? ':'.$fallback['port'] : '';
        }

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
