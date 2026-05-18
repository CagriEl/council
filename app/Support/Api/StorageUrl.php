<?php

namespace App\Support\Api;

final class StorageUrl
{
    public static function fromPath(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $relativePath = 'storage/'.ltrim($path, '/');

        return self::baseUrl().'/'.$relativePath;
    }

    private static function baseUrl(): string
    {
        $publicPrefix = trim((string) config('app.public_prefix'), '/');

        $request = request();
        if ($request !== null) {
            $baseUrl = rtrim($request->getSchemeAndHttpHost().$request->getBaseUrl(), '/');

            return self::appendPrefixIfNeeded($baseUrl, $publicPrefix);
        }

        $appUrl = rtrim((string) config('app.url'), '/');

        return self::appendPrefixIfNeeded($appUrl, $publicPrefix);
    }

    private static function appendPrefixIfNeeded(string $baseUrl, string $publicPrefix): string
    {
        if ($publicPrefix !== '' && ! str_ends_with($baseUrl, '/'.$publicPrefix)) {
            $baseUrl .= '/'.$publicPrefix;
        }

        return $baseUrl;
    }
}
