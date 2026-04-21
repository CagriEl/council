<?php

namespace App\Support\Api;

final class StorageUrl
{
    public static function fromPath(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return asset('storage/'.$path);
    }
}
