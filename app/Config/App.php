<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    /**
     * Base Site URL
     */
    public string $baseURL = 'http://puninarlogistic.test/';

    /**
     * Allowed Hostnames in the Site URL other than the hostname in the baseURL.
     */
    public array $allowedHostnames = [];

    /**
     * Index File
     */
    public string $indexPage = '';

    /**
     * URI PROTOCOL
     */
    public string $uriProtocol = 'REQUEST_URI';

    /**
     * Default Locale
     */
    public string $defaultLocale = 'en';

    /**
     * Negotiate Locale
     */
    public bool $negotiateLocale = false;

    /**
     * Supported Locales
     */
    public array $supportedLocales = ['en'];

    /**
     * Application Timezone
     */
    public string $appTimezone = 'Asia/Jakarta';

    /**
     * Default Character Set
     */
    public string $charset = 'UTF-8';

    /**
     * Force Global Secure Requests
     */
    public bool $forceGlobalSecureRequests = false;

    /**
     * Session Variables
     */
    public $sessionDriver            = 'CodeIgniter\Session\Handlers\FileHandler';
    public string $sessionCookieName       = 'ci_session';
    public int $sessionExpiration          = 7200;
    public string $sessionSavePath         = WRITEPATH . 'session';
    public bool $sessionMatchIP            = false;
    public int $sessionTimeToUpdate        = 300;
    public bool $sessionRegenerateDestroy  = false;

    /**
     * Security
     */
    public $CSRFTokenName  = 'csrf_token_name';
    public $CSRFHeaderName = 'X-CSRF-TOKEN';
    public $CSRFCookieName = 'csrf_cookie_name';
    public int $CSRFExpire       = 7200;
    public bool $CSRFRegenerate  = true;
    public bool $CSRFRedirect    = true;
    public string $CSRFSameSite  = 'Lax';

    /**
     * Content Security Policy
     */
    public bool $CSPEnabled = false;

    /**
     * Cookie settings
     */
    public $cookiePrefix   = '';
    public string $cookieDomain  = '';
    public string $cookiePath    = '/';
    public bool $cookieSecure    = false;
    public bool $cookieHTTPOnly  = true;
    public string $cookieSameSite = 'Lax';

    /**
     * Reverse Proxy IPs
     */
    public array $proxyIPs = [];

    /**
     * CORS settings
     */
    public $CORSEnabled           = false;
    public $CORSAllowedOrigins    = [];
    public  $CORSAllowedHeaders    = [];
    public $CORSAllowedMethods    = [];
    public  $CORSAllowCredentials  = false;
}