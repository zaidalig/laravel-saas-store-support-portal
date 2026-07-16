<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CDN / Media Storage — SaaS Store Portal
    |--------------------------------------------------------------------------
    |
    | CDN is preferred by default (CDN_ENABLED=true). When CDN_URL is empty
    | and S3 is not configured, uploads are saved to storage/app/public.
    |
    | Set CDN_URL to your CDN origin (e.g. CloudFront / Bunny / Cloudflare R2
    | public URL). Optionally set AWS_* + CDN_USE_S3=true to store on S3.
    |
    */

    'enabled' => env('CDN_ENABLED', true),

    'url' => env('CDN_URL'),

    'disk' => env('CDN_DISK', 's3'),

    'use_s3' => env('CDN_USE_S3', false),

    'prefix' => env('CDN_PREFIX', 'saas'),

    'folders' => [
        'products' => 'product-images',
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend asset CDNs (Bootstrap / Font Awesome)
    |--------------------------------------------------------------------------
    |
    | Default: load from CDN. Set ASSET_CDN=false to use local vendor copies
    | under public/ (place files at the local paths below if you disable CDN).
    |
    */

    'assets' => [
        'enabled' => env('ASSET_CDN', true),
        'bootstrap_css' => env('CDN_BOOTSTRAP_CSS', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'),
        'bootstrap_js' => env('CDN_BOOTSTRAP_JS', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'),
        'fontawesome' => env('CDN_FONTAWESOME', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'),
        'chartjs' => env('CDN_CHARTJS', 'https://cdn.jsdelivr.net/npm/chart.js'),
        'local' => [
            'bootstrap_css' => 'vendor/bootstrap/bootstrap.min.css',
            'bootstrap_js' => 'vendor/bootstrap/bootstrap.bundle.min.js',
            'fontawesome' => 'vendor/fontawesome/css/all.min.css',
            'chartjs' => 'vendor/chartjs/chart.umd.min.js',
        ],
    ],

];
