<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaStorage
{
    /**
     * CDN is preferred when enabled and a CDN URL (or S3 bucket) is configured.
     * Otherwise files are stored on the local public disk (storage/app/public).
     */
    public static function usingCdn(): bool
    {
        if (! (bool) config('cdn.enabled', true)) {
            return false;
        }

        return filled(config('cdn.url')) || self::s3Configured();
    }

    public static function s3Configured(): bool
    {
        return filled(config('filesystems.disks.s3.key'))
            && filled(config('filesystems.disks.s3.secret'))
            && filled(config('filesystems.disks.s3.bucket'));
    }

    public static function disk(): string
    {
        // Store on S3 only when CDN is active, CDN_USE_S3=true, and AWS is configured.
        // Otherwise always save under storage/app/public (CDN_URL still used for public URLs).
        if (self::usingCdn() && (bool) config('cdn.use_s3', false) && self::s3Configured()) {
            return (string) config('cdn.disk', 's3');
        }

        return 'public';
    }

    public static function store(UploadedFile $file, string $directory): string
    {
        return $file->store(trim($directory, '/'), self::disk());
    }

    public static function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        foreach (array_unique([self::disk(), 'public', 's3']) as $disk) {
            try {
                if (Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);
                }
            } catch (\Throwable) {
                // ignore disks that are not configured
            }
        }
    }

    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (self::usingCdn() && filled(config('cdn.url'))) {
            return rtrim((string) config('cdn.url'), '/').'/'.ltrim($path, '/');
        }

        return Storage::disk(self::disk() === 's3' ? 's3' : 'public')->url($path);
    }
}
