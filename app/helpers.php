<?php

use App\Services\MediaStorage;

if (! function_exists('media_url')) {
    function media_url(?string $path): ?string
    {
        return MediaStorage::url($path);
    }
}

if (! function_exists('asset_cdn')) {
    /**
     * Frontend asset URL: CDN when ASSET_CDN is enabled, otherwise a local asset path.
     */
    function asset_cdn(string $key, ?string $local = null): string
    {
        if (config('cdn.assets.enabled', true)) {
            return (string) config("cdn.assets.{$key}", $local ?? '');
        }

        return $local ? asset($local) : (string) config("cdn.assets.{$key}", '');
    }
}
