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

        return rtrim((string) config('app.url'), '/').'/duyurular?tip=resmi';
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
        $nodes = $xpath->query("//a[contains(concat(' ', normalize-space(@class), ' '), ' ann-list-card ')]");

        if (! $nodes instanceof \DOMNodeList) {
            return [];
        }

        $items = [];

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

            $items[] = [
                'title' => $title !== '' ? $title : 'Duyuru',
                'image_url' => $imageUrl,
                'detail_url' => $detailUrl,
            ];
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

        $appBase = rtrim((string) config('app.url'), '/');

        if (str_starts_with($pathOrUrl, '/')) {
            return $appBase.$pathOrUrl;
        }

        return $appBase.'/'.ltrim($pathOrUrl, '/');
    }
}
