<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to make reading things nicer
     * and simpler.
     */
    public array $aliases = [
        'csrf'          => \CodeIgniter\Filters\CSRF::class,
        'toolbar'       => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot'      => \CodeIgniter\Filters\Honeypot::class,
        'invalidchars'  => \CodeIgniter\Filters\InvalidChars::class,
        'secureheaders' => \CodeIgniter\Filters\SecureHeaders::class,
        'security'      => \App\Filters\SecurityFilter::class,
        'performance'   => \App\Filters\PerformanceFilter::class,
        'auth'          => \App\Filters\AuthFilter::class,
        'role'          => \App\Filters\RoleFilter::class,
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     */
    // public array $globals = [
    //     'before' => [
    //         'performance',
    //         'honeypot',
    //         'csrf' => ['except' => ['api/*']],
    //         'invalidchars',
    //         'security',
    //     ],
    //     'after' => [
    //         'performance',
    //         'toolbar',
    //         'honeypot',
    //         'secureheaders',
    //     ],
    // ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     */
    public array $filters = [];
}