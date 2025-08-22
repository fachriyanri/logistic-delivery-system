<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Stores the default settings for the ContentSecurityPolicy, if you
 * choose to use it. The values here will be read in and set as defaults
 * for the site. If needed, they can be overridden on a page-by-page basis.
 *
 * Suggested reference for explanations:
 *
 * @see https://www.html5rocks.com/en/tutorials/security/content-security-policy/
 */
class ContentSecurityPolicy extends BaseConfig
{
    //-------------------------------------------------------------------------
    // Broadbrush CSP management
    //-------------------------------------------------------------------------

    /**
     * Default CSP report context
     */
    public bool $reportOnly = false;

    /**
     * Specifies a URL where a browser will send reports
     * when a content security policy is violated.
     */
    public ?string $reportURI = null;

    /**
     * Instructs the browser to POST a reports of policy failures
     */
    public bool $upgradeInsecureRequests = false;

    //-------------------------------------------------------------------------
    // Sources allowed
    // Note: once you set a policy to 'none', it cannot be further restricted
    //-------------------------------------------------------------------------

    /**
     * Will default to self if not overridden
     *
     * @var string|string[]|null
     */
    public $defaultSrc = 'self';

    /**
     * Lists allowed scripts' URLs.
     *
     * @var string|string[]|null
     */
    public $scriptSrc = ['self', 'unsafe-inline'];

    /**
     * Lists allowed stylesheets' URLs.
     *
     * @var string|string[]|null
     */
    public $styleSrc = ['self', 'unsafe-inline', 'https://cdnjs.cloudflare.com', 'https://cdn.jsdelivr.net'];

    /**
     * Defines the origins from which images can be loaded.
     *
     * @var string|string[]|null
     */
    public $imageSrc = ['self', 'data:'];

    /**
     * Restricts the origins allowed to deliver video and audio.
     *
     * @var string|string[]|null
     */
    public $mediaSrc = null;

    /**
     * Lists valid endpoints for submission from <form> tags.
     *
     * @var string|string[]|null
     */
    public $formAction = 'self';

    /**
     * Specifies the sources that can embed the current page.
     * This directive applies to <frame>, <iframe>, <embed>,
     * and <applet> tags. This directive can't be used in
     * <meta> tags and applies only to non-HTML resources.
     *
     * @var string|string[]|null
     */
    public $frameAncestors = null;

    /**
     * The frame-src directive restricts the URLs which may
     * be loaded into nested browsing contexts.
     *
     * @var string|string[]|null
     */
    public $frameSrc = null;

    /**
     * Restricts the origins allowed to deliver fonts.
     *
     * @var string|string[]|null
     */
    public $fontSrc = ['self', 'https://cdnjs.cloudflare.com', 'https://fonts.googleapis.com', 'https://fonts.gstatic.com'];

    /**
     * Lists valid sources for XMLHttpRequest (AJAX), WebSocket,
     * and EventSource connections.
     *
     * @var string|string[]|null
     */
    public $connectSrc = 'self';

    /**
     * Lists valid sources for loading objects, embed, and applet elements.
     *
     * @var string|string[]|null
     */
    public $objectSrc = 'none';

    /**
     * Lists valid sources for worker scripts loaded via
     * Worker(), SharedWorker(), or ServiceWorker().
     *
     * @var string|string[]|null
     */
    public $workerSrc = null;

    /**
     * Lists valid sources for request prefetch and prerendering,
     * for example via the link tag with rel="prefetch" or rel="prerender":
     *
     * @var string|string[]|null
     */
    public $prefetchSrc = null;

    /**
     * List of actions allowed.
     *
     * @var string|string[]|null
     */
    public $baseURI = 'self';

    //-------------------------------------------------------------------------
    // Sandbox
    //-------------------------------------------------------------------------

    /**
     * Specifies an HTML sandbox policy that the user agent applies to
     * the protected resource.
     *
     * @var string|string[]|null
     */
    public $sandbox = false;
}