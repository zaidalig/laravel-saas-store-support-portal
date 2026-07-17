<?php

use App\Models\Setting;
use App\Services\MediaStorage;
use Illuminate\Support\Facades\Cache;

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

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('app_settings', 60, function () {
            return Setting::query()->pluck('value', 'key')->all();
        });

        return $settings[$key] ?? $default;
    }
}

if (! function_exists('next_number')) {
    function next_number(string $prefix): string
    {
        return strtoupper($prefix).'-'.now()->format('ymd').'-'.strtoupper(substr(uniqid(), -5));
    }
}
