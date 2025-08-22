<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="<?= $meta_description ?? 'Mobile Logistics Tracking' ?>">
    <meta name="author" content="<?= COMPANY_NAME ?>">
    <title><?= $title ?? 'Mobile Tracking' ?> - <?= APP_NAME ?></title>
    
    <!-- Mobile Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#1a2332">
    <meta name="msapplication-navbutton-color" content="#1a2332">
    <meta name="apple-mobile-web-app-title" content="<?= APP_NAME ?>">
    
    <!-- Favicon and Touch Icons -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/logo.jpg') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/images/puninar_logo.webp') ?>">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6.x -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Mobile CSS -->
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
    
    <!-- Additional CSS -->
    <?= $this->renderSection('css') ?>
    
    <style>
    /* Mobile-specific overrides */
    body {
        font-size: 16px; /* Prevent zoom on iOS */
        -webkit-text-size-adjust: 100%;
        -webkit-font-smoothing: antialiased;
    }
    
    .mobile-container {
        min-height: 100vh;
        background: var(--gray-50);
    }
    
    .mobile-header {
        background: var(--primary-color);
        color: white;
        padding: 1rem;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: var(--shadow-md);
    }
    
    .mobile-content {
        flex: 1;
        padding: 0;
    }
    
    .mobile-footer {
        background: white;
        border-top: 1px solid var(--gray-200);
        padding: 1rem;
        text-align: center;
    }
    
    /* Safe area handling for notched devices */
    @supports (padding: max(0px)) {
        .mobile-header {
            padding-top: max(1rem, env(safe-area-inset-top));
        }
        
        .mobile-footer {
            padding-bottom: max(1rem, env(safe-area-inset-bottom));
        }
    }
    
    /* Landscape orientation adjustments */
    @media (orientation: landscape) and (max-height: 500px) {
        .mobile-header {
            padding: 0.5rem 1rem;
        }
        
        .mobile-content {
            padding-top: 0.5rem;
        }
    }
    </style>
</head>
<body class="mobile-body">
    <!-- Mobile Container -->
    <div class="mobile-container d-flex flex-column">
        <!-- Mobile Header -->
        <header class="mobile-header">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <img src="<?= base_url('assets/images/puninar_logo.webp') ?>" 
                         alt="<?= COMPANY_NAME ?>" 
                         height="30" 
                         class="me-2">
                    <div>
                        <div class="fw-bold"><?= APP_NAME ?></div>
                        <small class="opacity-75"><?= COMPANY_NAME ?></small>
                    </div>
                </div>
                
                <div class="mobile-header-actions">
                    <?= $this->renderSection('header_actions') ?>
                </div>
            </div>
        </header>
        
        <!-- Mobile Content -->
        <main class="mobile-content flex-grow-1">
            <?= $this->renderSection('content') ?>
        </main>
        
        <!-- Mobile Footer -->
        <footer class="mobile-footer">
            <small class="text-muted">
                © <?= date('Y') ?> <?= COMPANY_NAME ?>. All rights reserved.
            </small>
        </footer>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer">
        <!-- Toasts will be dynamically added here -->
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay d-none">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="loading-text mt-2">Loading...</div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jsQR Library for QR Code scanning -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    
    <!-- Progressive Enhancement -->
    <script src="<?= base_url('assets/js/progressive-enhancement.js') ?>"></script>
    
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    
    <!-- Mobile-specific JavaScript -->
    <script>
    // Mobile-specific initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Prevent zoom on double tap
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
        
        // Handle viewport height changes (mobile keyboard)
        function setViewportHeight() {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        }
        
        setViewportHeight();
        window.addEventListener('resize', setViewportHeight);
        window.addEventListener('orientationchange', setViewportHeight);
        
        // Add mobile class to body
        document.body.classList.add('mobile-device');
        
        // Handle back button
        window.addEventListener('popstate', function(event) {
            // Custom back button handling if needed
        });
        
        // Add pull-to-refresh if supported
        if ('serviceWorker' in navigator) {
            // Register service worker for offline support
            navigator.serviceWorker.register('/sw.js').catch(console.error);
        }
        
        // Handle online/offline status
        function updateOnlineStatus() {
            const status = navigator.onLine ? 'online' : 'offline';
            document.body.classList.toggle('offline', !navigator.onLine);
            
            if (window.app && window.app.showToast) {
                const message = navigator.onLine ? 'Back online' : 'You are offline';
                const type = navigator.onLine ? 'success' : 'warning';
                window.app.showToast(message, type, '', { delay: 3000 });
            }
        }
        
        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
        
        // Initial status check
        updateOnlineStatus();
    });
    
    // Mobile utility functions
    window.mobileUtils = {
        // Vibrate device if supported
        vibrate: function(pattern) {
            if ('vibrate' in navigator) {
                navigator.vibrate(pattern);
            }
        },
        
        // Share content if supported
        share: async function(data) {
            if (navigator.share) {
                try {
                    await navigator.share(data);
                    return true;
                } catch (error) {
                    console.warn('Share failed:', error);
                    return false;
                }
            }
            return false;
        },
        
        // Copy to clipboard
        copyToClipboard: async function(text) {
            if (navigator.clipboard) {
                try {
                    await navigator.clipboard.writeText(text);
                    return true;
                } catch (error) {
                    console.warn('Clipboard write failed:', error);
                    return false;
                }
            }
            return false;
        },
        
        // Get device info
        getDeviceInfo: function() {
            return {
                userAgent: navigator.userAgent,
                platform: navigator.platform,
                language: navigator.language,
                cookieEnabled: navigator.cookieEnabled,
                onLine: navigator.onLine,
                screenWidth: screen.width,
                screenHeight: screen.height,
                windowWidth: window.innerWidth,
                windowHeight: window.innerHeight,
                devicePixelRatio: window.devicePixelRatio || 1
            };
        },
        
        // Check if device is in landscape mode
        isLandscape: function() {
            return window.innerWidth > window.innerHeight;
        },
        
        // Check if device supports touch
        isTouchDevice: function() {
            return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        }
    };
    </script>
    
    <!-- Additional JS -->
    <?= $this->renderSection('js') ?>
</body>
</html>