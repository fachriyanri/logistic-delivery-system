<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Assets extends BaseConfig
{
    /**
     * Enable asset minification
     */
    public bool $minifyAssets = ENVIRONMENT === 'production';

    /**
     * Enable asset compression
     */
    public bool $compressAssets = ENVIRONMENT === 'production';

    /**
     * Asset cache duration (in seconds)
     */
    public int $cacheDuration = 31536000; // 1 year

    /**
     * CSS files to minify and combine
     */
    public array $cssFiles = [
        'bootstrap' => [
            'assets/css/bootstrap.min.css',
        ],
        'app' => [
            'assets/css/app.css',
        ],
        'components' => [
            'assets/css/components.css',
        ]
    ];

    /**
     * JavaScript files to minify and combine
     */
    public array $jsFiles = [
        'bootstrap' => [
            'assets/js/bootstrap.bundle.min.js',
        ],
        'app' => [
            'assets/js/app.js',
            'assets/js/datatable.js',
            'assets/js/form-validator.js',
        ]
    ];

    /**
     * Image optimization settings
     */
    public array $imageOptimization = [
        'jpeg_quality' => 85,
        'png_compression' => 9,
        'webp_quality' => 80,
        'max_width' => 1920,
        'max_height' => 1080,
    ];

    /**
     * CDN settings
     */
    public array $cdn = [
        'enabled' => false,
        'base_url' => '',
        'assets' => [
            'css' => true,
            'js' => true,
            'images' => true,
            'fonts' => true,
        ]
    ];

    /**
     * Asset versioning for cache busting
     */
    public bool $enableVersioning = true;

    /**
     * Preload critical resources
     */
    public array $preloadResources = [
        'css' => [
            'assets/css/app.css',
        ],
        'js' => [
            'assets/js/app.js',
        ],
        'fonts' => [
            // Add critical fonts here
        ]
    ];

    /**
     * Lazy load settings
     */
    public array $lazyLoad = [
        'images' => true,
        'iframes' => true,
        'threshold' => '50px',
    ];
}