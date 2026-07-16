<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return cache()->remember("setting_{$key}", 60, fn () => Setting::where('key', $key)->value('value') ?? $default);
    }
}

if (! function_exists('next_number')) {
    function next_number(string $prefix): string
    {
        return $prefix.'-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}