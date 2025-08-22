/**
 * Main Application JavaScript
 * Yusen Logistics Inspired Design System
 */

class App {
    constructor() {
        this.browserInfo = this.detectBrowser();
        this.init();
    }

    init() {
        this.setupBrowserCompatibility();
        this.setupEventListeners();
        this.initializeComponents();
        this.setupAjaxDefaults();
    }

    detectBrowser() {
        const userAgent = navigator.userAgent;
        const isChrome = /Chrome/.test(userAgent) && /Google Inc/.test(navigator.vendor);
        const isFirefox = /Firefox/.test(userAgent);
        const isSafari = /Safari/.test(userAgent) && /Apple Computer/.test(navigator.vendor);
        const isEdge = /Edg/.test(userAgent);
        const isIE = /Trident/.test(userAgent) || /MSIE/.test(userAgent);
        
        return {
            isChrome,
            isFirefox,
            isSafari,
            isEdge,
            isIE,
            supportsES6: this.checkES6Support(),
            supportsFetch: 'fetch' in window,
            supportsCustomProperties: this.checkCustomPropertiesSupport(),
            supportsGrid: this.checkGridSupport(),
            supportsFlexbox: this.checkFlexboxSupport()
        };
    }

    checkES6Support() {
        try {
            new Function('(a = 0) => a');
            return true;
        } catch (err) {
            return false;
        }
    }

    checkCustomPropertiesSupport() {
        return window.CSS && CSS.supports && CSS.supports('color', 'var(--fake-var)');
    }

    checkGridSupport() {
        return CSS.supports('display', 'grid');
    }

    checkFlexboxSupport() {
        return CSS.supports('display', 'flex');
    }

    setupBrowserCompatibility() {
        // Add browser classes to body
        document.body.classList.add(this.getBrowserClass());
        
        // Add feature support classes
        if (!this.browserInfo.supportsCustomProperties) {
            document.body.classList.add('no-custom-properties');
        }
        
        if (!this.browserInfo.supportsGrid) {
            document.body.classList.add('no-grid');
        }
        
        if (!this.browserInfo.supportsFlexbox) {
            document.body.classList.add('no-flexbox');
        }
        
        // Load polyfills if needed
        this.loadPolyfills();
        
        // Apply browser-specific fixes
        this.applyBrowserFixes();
    }

    getBrowserClass() {
        if (this.browserInfo.isChrome) return 'browser-chrome';
        if (this.browserInfo.isFirefox) return 'browser-firefox';
        if (this.browserInfo.isSafari) return 'browser-safari';
        if (this.browserInfo.isEdge) return 'browser-edge';
        if (this.browserInfo.isIE) return 'browser-ie';
        return 'browser-unknown';
    }

    loadPolyfills() {
        const polyfills = [];
        
        // Fetch polyfill for older browsers
        if (!this.browserInfo.supportsFetch) {
            polyfills.push(this.loadFetchPolyfill());
        }
        
        // Custom properties polyfill for IE
        if (!this.browserInfo.supportsCustomProperties) {
            polyfills.push(this.loadCustomPropertiesPolyfill());
        }
        
        // Promise polyfill for IE
        if (!window.Promise) {
            polyfills.push(this.loadPromisePolyfill());
        }
        
        return Promise.all(polyfills);
    }

    loadFetchPolyfill() {
        return new Promise((resolve) => {
            if (window.fetch) {
                resolve();
                return;
            }
            
            // Simple fetch polyfill
            window.fetch = function(url, options = {}) {
                return new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.open(options.method || 'GET', url);
                    
                    if (options.headers) {
                        Object.keys(options.headers).forEach(key => {
                            xhr.setRequestHeader(key, options.headers[key]);
                        });
                    }
                    
                    xhr.onload = () => {
                        resolve({
                            ok: xhr.status >= 200 && xhr.status < 300,
                            status: xhr.status,
                            json: () => Promise.resolve(JSON.parse(xhr.responseText)),
                            text: () => Promise.resolve(xhr.responseText)
                        });
                    };
                    
                    xhr.onerror = () => reject(new Error('Network error'));
                    xhr.send(options.body);
                });
            };
            
            resolve();
        });
    }

    loadCustomPropertiesPolyfill() {
        return new Promise((resolve) => {
            // Simple CSS custom properties fallback
            const style = document.createElement('style');
            style.textContent = `
                .no-custom-properties {
                    --primary-color: #1a2332;
                    --secondary-color: #ff6b35;
                    --success-color: #28a745;
                    --warning-color: #ffc107;
                    --danger-color: #dc3545;
                }
            `;
            document.head.appendChild(style);
            resolve();
        });
    }

    loadPromisePolyfill() {
        return new Promise((resolve) => {
            if (window.Promise) {
                resolve();
                return;
            }
            
            // Simple Promise polyfill
            window.Promise = function(executor) {
                const self = this;
                self.state = 'pending';
                self.value = undefined;
                self.handlers = [];
                
                function resolve(result) {
                    if (self.state === 'pending') {
                        self.state = 'fulfilled';
                        self.value = result;
                        self.handlers.forEach(handle);
                        self.handlers = null;
                    }
                }
                
                function reject(error) {
                    if (self.state === 'pending') {
                        self.state = 'rejected';
                        self.value = error;
                        self.handlers.forEach(handle);
                        self.handlers = null;
                    }
                }
                
                function handle(handler) {
                    if (self.state === 'pending') {
                        self.handlers.push(handler);
                    } else {
                        if (self.state === 'fulfilled' && typeof handler.onFulfilled === 'function') {
                            handler.onFulfilled(self.value);
                        }
                        if (self.state === 'rejected' && typeof handler.onRejected === 'function') {
                            handler.onRejected(self.value);
                        }
                    }
                }
                
                this.then = function(onFulfilled, onRejected) {
                    return new Promise((resolve, reject) => {
                        handle({
                            onFulfilled: function(result) {
                                try {
                                    resolve(onFulfilled ? onFulfilled(result) : result);
                                } catch (ex) {
                                    reject(ex);
                                }
                            },
                            onRejected: function(error) {
                                try {
                                    resolve(onRejected ? onRejected(error) : error);
                                } catch (ex) {
                                    reject(ex);
                                }
                            }
                        });
                    });
                };
                
                executor(resolve, reject);
            };
            
            resolve();
        });
    }

    applyBrowserFixes() {
        // Internet Explorer fixes
        if (this.browserInfo.isIE) {
            this.applyIEFixes();
        }
        
        // Safari fixes
        if (this.browserInfo.isSafari) {
            this.applySafariFixes();
        }
        
        // Firefox fixes
        if (this.browserInfo.isFirefox) {
            this.applyFirefoxFixes();
        }
        
        // Edge fixes
        if (this.browserInfo.isEdge) {
            this.applyEdgeFixes();
        }
    }

    applyIEFixes() {
        // Fix flexbox issues in IE
        const flexContainers = document.querySelectorAll('.d-flex');
        flexContainers.forEach(container => {
            container.style.display = '-ms-flexbox';
        });
        
        // Fix CSS Grid fallback
        const gridContainers = document.querySelectorAll('.row');
        gridContainers.forEach(container => {
            container.classList.add('grid-fallback');
        });
        
        // Fix object-fit for images
        const images = document.querySelectorAll('img');
        images.forEach(img => {
            if (img.style.objectFit) {
                // Fallback for object-fit
                const parent = img.parentElement;
                parent.style.overflow = 'hidden';
                img.style.width = '100%';
                img.style.height = '100%';
            }
        });
    }

    applySafariFixes() {
        // Fix iOS viewport height issue
        if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
            const setVH = () => {
                const vh = window.innerHeight * 0.01;
                document.documentElement.style.setProperty('--vh', `${vh}px`);
            };
            
            setVH();
            window.addEventListener('resize', setVH);
            window.addEventListener('orientationchange', setVH);
        }
        
        // Fix Safari button appearance
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(btn => {
            btn.style.webkitAppearance = 'none';
        });
    }

    applyFirefoxFixes() {
        // Fix Firefox input number styling
        const numberInputs = document.querySelectorAll('input[type="number"]');
        numberInputs.forEach(input => {
            input.style.mozAppearance = 'textfield';
        });
        
        // Fix Firefox button focus
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(btn => {
            btn.addEventListener('focus', (e) => {
                e.target.style.outline = '2px solid var(--primary-color)';
                e.target.style.outlineOffset = '2px';
            });
            
            btn.addEventListener('blur', (e) => {
                e.target.style.outline = 'none';
            });
        });
    }

    applyEdgeFixes() {
        // Fix Edge Legacy issues
        if (navigator.userAgent.indexOf('Edge/') > -1) {
            // Add specific Edge Legacy fixes
            document.body.classList.add('edge-legacy');
        }
    }

    setupEventListeners() {
        // Global click handlers
        document.addEventListener('click', this.handleGlobalClicks.bind(this));
        
        // Form submission handlers
        document.addEventListener('submit', this.handleFormSubmissions.bind(this));
        
        // Window resize handler
        window.addEventListener('resize', this.handleResize.bind(this));
        
        // Page visibility change
        document.addEventListener('visibilitychange', this.handleVisibilityChange.bind(this));
    }

    initializeComponents() {
        // Initialize tooltips
        this.initTooltips();
        
        // Initialize popovers
        this.initPopovers();
        
        // Initialize auto-hide alerts
        this.initAutoHideAlerts();
        
        // Initialize loading states
        this.initLoadingStates();
        
        // Initialize mobile features
        this.initMobileFeatures();
        
        // Initialize touch gestures
        this.initTouchGestures();
        
        // Initialize viewport adjustments
        this.adjustViewportHeight();
    }

    setupAjaxDefaults() {
        // Set default CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            // Set default headers for fetch requests
            window.fetch = new Proxy(window.fetch, {
                apply: (target, thisArg, argumentsList) => {
                    const [url, options = {}] = argumentsList;
                    
                    if (!options.headers) {
                        options.headers = {};
                    }
                    
                    if (typeof options.headers.append === 'function') {
                        options.headers.append('X-CSRF-TOKEN', csrfToken.content);
                    } else {
                        options.headers['X-CSRF-TOKEN'] = csrfToken.content;
                    }
                    
                    return target.apply(thisArg, [url, options]);
                }
            });
        }
    }

    handleGlobalClicks(event) {
        const target = event.target.closest('[data-action]');
        if (!target) return;

        const action = target.dataset.action;
        
        switch (action) {
            case 'confirm-delete':
                this.handleConfirmDelete(event, target);
                break;
            case 'toggle-sidebar':
                this.toggleSidebar();
                break;
            case 'show-loading':
                this.showLoading();
                break;
            case 'hide-loading':
                this.hideLoading();
                break;
        }
    }

    handleFormSubmissions(event) {
        const form = event.target;
        
        // Add loading state to submit buttons
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.disabled) {
            this.setButtonLoading(submitBtn, true);
            
            // Remove loading state after form submission
            setTimeout(() => {
                this.setButtonLoading(submitBtn, false);
            }, 2000);
        }
    }

    handleResize() {
        // Handle responsive behavior
        this.updateSidebarState();
        this.updateMobileLayout();
        this.adjustViewportHeight();
    }

    handleVisibilityChange() {
        if (document.hidden) {
            // Page is hidden
            console.log('Page hidden');
        } else {
            // Page is visible
            console.log('Page visible');
        }
    }

    // Component Initializers
    initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    initPopovers() {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }

    initAutoHideAlerts() {
        const alerts = document.querySelectorAll('.alert:not(.alert-danger)');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 5000);
        });
    }

    initLoadingStates() {
        // Initialize skeleton loaders
        const skeletons = document.querySelectorAll('.skeleton');
        skeletons.forEach(skeleton => {
            setTimeout(() => {
                skeleton.classList.remove('skeleton');
            }, 1000);
        });
    }

    // Utility Methods
    showLoading(message = 'Loading...') {
        const overlay = document.getElementById('loadingOverlay');
        const text = overlay.querySelector('.loading-text');
        
        if (text) {
            text.textContent = message;
        }
        
        overlay.classList.remove('d-none');
    }

    hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        overlay.classList.add('d-none');
    }

    setButtonLoading(button, isLoading) {
        if (isLoading) {
            button.disabled = true;
            const originalText = button.innerHTML;
            button.dataset.originalText = originalText;
            
            const spinner = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>';
            const loadingText = button.dataset.loadingText || 'Loading...';
            
            button.innerHTML = spinner + loadingText;
        } else {
            button.disabled = false;
            if (button.dataset.originalText) {
                button.innerHTML = button.dataset.originalText;
            }
        }
    }

    handleConfirmDelete(event, target) {
        event.preventDefault();
        
        const message = target.dataset.message || 'Are you sure you want to delete this item?';
        const url = target.href || target.dataset.url;
        
        if (confirm(message)) {
            if (url) {
                window.location.href = url;
            }
        }
    }

    toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.toggle('collapsed');
        }
    }

    updateSidebarState() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        if (window.innerWidth < 992) {
            // Mobile view
            if (sidebar) sidebar.classList.add('d-none');
            if (mainContent) mainContent.style.marginLeft = '0';
        } else {
            // Desktop view
            if (sidebar) sidebar.classList.remove('d-none');
            if (mainContent) mainContent.style.marginLeft = 'var(--sidebar-width)';
        }
    }

    // Mobile-specific methods
    initMobileFeatures() {
        this.isMobile = window.innerWidth < 768;
        this.isTouch = 'ontouchstart' in window;
        
        if (this.isMobile) {
            this.initMobileNavigation();
            this.initMobileFormEnhancements();
            this.initMobileTableEnhancements();
            this.initPullToRefresh();
        }
        
        if (this.isTouch) {
            document.body.classList.add('touch-device');
            this.initTouchOptimizations();
        }
    }

    initMobileNavigation() {
        // Auto-close mobile sidebar when clicking outside
        document.addEventListener('click', (event) => {
            const mobileSidebar = document.getElementById('mobileSidebar');
            const navToggler = document.querySelector('.navbar-toggler');
            
            if (mobileSidebar && !mobileSidebar.contains(event.target) && 
                !navToggler.contains(event.target)) {
                const offcanvas = bootstrap.Offcanvas.getInstance(mobileSidebar);
                if (offcanvas) {
                    offcanvas.hide();
                }
            }
        });

        // Add swipe gesture to close sidebar
        this.addSwipeToClose('#mobileSidebar');
    }

    initMobileFormEnhancements() {
        // Convert form layouts for mobile
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            if (window.innerWidth < 768) {
                this.convertFormToMobile(form);
            }
        });

        // Add mobile form validation
        this.initMobileValidation();
    }

    initMobileTableEnhancements() {
        const tables = document.querySelectorAll('.data-table');
        tables.forEach(table => {
            if (window.innerWidth < 576) {
                this.convertTableToCards(table);
            }
        });
    }

    convertFormToMobile(form) {
        // Stack form elements vertically on mobile
        const rows = form.querySelectorAll('.row');
        rows.forEach(row => {
            if (window.innerWidth < 768) {
                row.classList.add('mobile-form-row');
                const cols = row.querySelectorAll('[class*="col-"]');
                cols.forEach(col => {
                    col.className = 'col-12 mobile-form-group';
                });
            }
        });

        // Add mobile action bar for form buttons
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn && window.innerWidth < 768) {
            this.createMobileActionBar(form, submitBtn);
        }
    }

    createMobileActionBar(form, submitBtn) {
        const existingActionBar = form.querySelector('.mobile-action-bar');
        if (existingActionBar) return;

        const actionBar = document.createElement('div');
        actionBar.className = 'mobile-action-bar';
        
        // Move submit button to action bar
        const btnClone = submitBtn.cloneNode(true);
        btnClone.classList.add('btn-primary');
        
        // Add cancel button
        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'btn btn-outline-secondary';
        cancelBtn.innerHTML = '<i class="fas fa-times me-2"></i>Cancel';
        cancelBtn.onclick = () => window.history.back();

        actionBar.appendChild(cancelBtn);
        actionBar.appendChild(btnClone);
        
        // Hide original submit button
        submitBtn.style.display = 'none';
        
        // Append to form
        form.appendChild(actionBar);
    }

    convertTableToCards(table) {
        const wrapper = table.closest('.table-responsive');
        if (!wrapper) return;

        wrapper.classList.add('table-responsive-stack');
        
        // Add data labels for mobile card layout
        const headers = table.querySelectorAll('thead th');
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, index) => {
                if (headers[index]) {
                    cell.setAttribute('data-label', headers[index].textContent.trim());
                }
            });
        });
    }

    initTouchGestures() {
        // Add swipe gesture support
        let startX, startY, startTime;
        
        document.addEventListener('touchstart', (e) => {
            const touch = e.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
            startTime = Date.now();
        }, { passive: true });
        
        document.addEventListener('touchend', (e) => {
            if (!startX || !startY) return;
            
            const touch = e.changedTouches[0];
            const endX = touch.clientX;
            const endY = touch.clientY;
            const endTime = Date.now();
            
            const deltaX = endX - startX;
            const deltaY = endY - startY;
            const deltaTime = endTime - startTime;
            
            // Check for swipe gesture
            if (Math.abs(deltaX) > Math.abs(deltaY) && 
                Math.abs(deltaX) > 50 && 
                deltaTime < 300) {
                
                const direction = deltaX > 0 ? 'right' : 'left';
                this.handleSwipeGesture(e.target, direction, deltaX);
            }
            
            // Reset
            startX = startY = null;
        }, { passive: true });
    }

    handleSwipeGesture(target, direction, distance) {
        // Handle swipe on swipeable items
        const swipeItem = target.closest('.swipe-item');
        if (swipeItem) {
            if (direction === 'left' && Math.abs(distance) > 100) {
                swipeItem.classList.add('swiped');
                this.addHapticFeedback('light');
            } else if (direction === 'right') {
                swipeItem.classList.remove('swiped');
            }
        }

        // Handle sidebar swipe
        if (direction === 'right' && distance > 100 && window.innerWidth < 992) {
            const mobileSidebar = document.getElementById('mobileSidebar');
            if (mobileSidebar) {
                const offcanvas = new bootstrap.Offcanvas(mobileSidebar);
                offcanvas.show();
                this.addHapticFeedback('medium');
            }
        }
    }

    addSwipeToClose(selector) {
        const element = document.querySelector(selector);
        if (!element) return;

        let startX;
        
        element.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        }, { passive: true });
        
        element.addEventListener('touchmove', (e) => {
            if (!startX) return;
            
            const currentX = e.touches[0].clientX;
            const deltaX = startX - currentX;
            
            if (deltaX > 50) {
                const offcanvas = bootstrap.Offcanvas.getInstance(element);
                if (offcanvas) {
                    offcanvas.hide();
                    this.addHapticFeedback('light');
                }
                startX = null;
            }
        }, { passive: true });
    }

    initPullToRefresh() {
        const refreshableElements = document.querySelectorAll('.pull-to-refresh');
        
        refreshableElements.forEach(element => {
            let startY, currentY, isPulling = false;
            
            element.addEventListener('touchstart', (e) => {
                if (element.scrollTop === 0) {
                    startY = e.touches[0].clientY;
                }
            }, { passive: true });
            
            element.addEventListener('touchmove', (e) => {
                if (!startY) return;
                
                currentY = e.touches[0].clientY;
                const deltaY = currentY - startY;
                
                if (deltaY > 0 && element.scrollTop === 0) {
                    isPulling = true;
                    element.classList.add('pulling');
                    
                    if (deltaY > 80) {
                        element.classList.add('ready-to-refresh');
                    }
                }
            }, { passive: true });
            
            element.addEventListener('touchend', () => {
                if (isPulling && element.classList.contains('ready-to-refresh')) {
                    this.triggerRefresh(element);
                    this.addHapticFeedback('medium');
                }
                
                element.classList.remove('pulling', 'ready-to-refresh');
                isPulling = false;
                startY = null;
            });
        });
    }

    triggerRefresh(element) {
        const refreshCallback = element.dataset.refreshCallback;
        if (refreshCallback && window[refreshCallback]) {
            window[refreshCallback]();
        } else {
            // Default refresh behavior
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }

    initTouchOptimizations() {
        // Disable hover effects on touch devices
        document.body.classList.add('touch-device');
        
        // Add touch-friendly focus styles
        const focusableElements = document.querySelectorAll('button, input, select, textarea, a');
        focusableElements.forEach(element => {
            element.addEventListener('focus', () => {
                element.classList.add('touch-focused');
            });
            
            element.addEventListener('blur', () => {
                element.classList.remove('touch-focused');
            });
        });
    }

    initMobileValidation() {
        // Enhanced validation for mobile forms
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateMobileForm(form)) {
                    e.preventDefault();
                    this.addHapticFeedback('heavy');
                }
            });
        });
    }

    validateMobileForm(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.showMobileFieldError(field);
                isValid = false;
            } else {
                this.clearMobileFieldError(field);
            }
        });
        
        if (!isValid) {
            // Scroll to first error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
        }
        
        return isValid;
    }

    showMobileFieldError(field) {
        field.classList.add('is-invalid');
        
        // Add shake animation
        field.classList.add('shake');
        setTimeout(() => {
            field.classList.remove('shake');
        }, 500);
    }

    clearMobileFieldError(field) {
        field.classList.remove('is-invalid');
    }

    updateMobileLayout() {
        const wasMobile = this.isMobile;
        this.isMobile = window.innerWidth < 768;
        
        if (wasMobile !== this.isMobile) {
            // Layout changed, reinitialize mobile features
            if (this.isMobile) {
                this.initMobileFeatures();
            } else {
                this.cleanupMobileFeatures();
            }
        }
    }

    cleanupMobileFeatures() {
        // Remove mobile-specific classes and elements
        const mobileActionBars = document.querySelectorAll('.mobile-action-bar');
        mobileActionBars.forEach(bar => bar.remove());
        
        const hiddenSubmitBtns = document.querySelectorAll('button[type="submit"][style*="display: none"]');
        hiddenSubmitBtns.forEach(btn => btn.style.display = '');
        
        document.body.classList.remove('mobile-layout');
    }

    adjustViewportHeight() {
        // Fix viewport height issues on mobile browsers
        if (this.isMobile) {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        }
    }

    addHapticFeedback(intensity = 'light') {
        // Simulate haptic feedback with visual cues
        if ('vibrate' in navigator) {
            const patterns = {
                light: [10],
                medium: [20],
                heavy: [30]
            };
            navigator.vibrate(patterns[intensity] || patterns.light);
        }
        
        // Visual feedback
        document.body.classList.add(`haptic-${intensity}`);
        setTimeout(() => {
            document.body.classList.remove(`haptic-${intensity}`);
        }, 200);
    }

    // Toast Notifications
    showToast(message, type = 'info', duration = 5000) {
        const toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) return;

        const toastId = 'toast-' + Date.now();
        const iconClass = this.getToastIcon(type);
        const bgClass = this.getToastBgClass(type);

        const toastHTML = `
            <div id="${toastId}" class="toast ${bgClass}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="${iconClass} me-2"></i>
                    <strong class="me-auto">${this.getToastTitle(type)}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: duration });
        
        toast.show();
        
        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    getToastIcon(type) {
        const icons = {
            success: 'fas fa-check-circle text-success',
            error: 'fas fa-exclamation-circle text-danger',
            warning: 'fas fa-exclamation-triangle text-warning',
            info: 'fas fa-info-circle text-info'
        };
        return icons[type] || icons.info;
    }

    getToastBgClass(type) {
        const classes = {
            success: 'bg-light border-success',
            error: 'bg-light border-danger',
            warning: 'bg-light border-warning',
            info: 'bg-light border-info'
        };
        return classes[type] || classes.info;
    }

    getToastTitle(type) {
        const titles = {
            success: 'Success',
            error: 'Error',
            warning: 'Warning',
            info: 'Information'
        };
        return titles[type] || titles.info;
    }

    // AJAX Helper Methods
    async makeRequest(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        const mergedOptions = { ...defaultOptions, ...options };
        
        try {
            this.showLoading();
            const response = await fetch(url, mergedOptions);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Request failed:', error);
            this.showToast('Request failed. Please try again.', 'error');
            throw error;
        } finally {
            this.hideLoading();
        }
    }

    // Form Validation Helpers
    validateForm(form) {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showFieldError(input, 'This field is required');
                isValid = false;
            } else {
                this.clearFieldError(input);
            }
        });

        return isValid;
    }

    showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        let feedback = field.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }
        
        feedback.textContent = message;
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.app = new App();
});

// Global utility functions
window.showToast = function(message, type = 'info', duration = 5000) {
    if (window.app) {
        window.app.showToast(message, type, duration);
    }
};

window.showLoading = function(message = 'Loading...') {
    if (window.app) {
        window.app.showLoading(message);
    }
};

window.hideLoading = function() {
    if (window.app) {
        window.app.hideLoading();
    }
};

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = App;
}    // 
Enhanced Toast Notifications
    createToast(message, type = 'info', title = '', options = {}) {
        const toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) return;

        const toastId = 'toast-' + Date.now();
        const defaultOptions = {
            delay: 5000,
            autohide: true,
            showTime: true
        };
        
        const config = { ...defaultOptions, ...options };
        
        const toastHTML = `
            <div id="${toastId}" class="toast ${this.getToastBgClass(type)}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${config.delay}" data-bs-autohide="${config.autohide}">
                ${title ? `
                <div class="toast-header">
                    <i class="${this.getToastIcon(type)} me-2"></i>
                    <strong class="me-auto">${title}</strong>
                    ${config.showTime ? `<small class="text-muted">${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</small>` : ''}
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                ` : ''}
                <div class="toast-body d-flex align-items-center">
                    ${!title ? `<i class="${this.getToastIcon(type)} me-3 flex-shrink-0"></i>` : ''}
                    <div class="flex-grow-1">${message}</div>
                    ${!title ? '<button type="button" class="btn-close ms-3" data-bs-dismiss="toast" aria-label="Close"></button>' : ''}
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { 
            delay: config.delay,
            autohide: config.autohide 
        });
        
        toast.show();
        
        // Add animation class
        toastElement.classList.add('slide-in-right');
        
        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });

        return toast;
    }

    getToastBgClass(type) {
        const classes = {
            success: 'bg-success-subtle border-success',
            error: 'bg-danger-subtle border-danger',
            warning: 'bg-warning-subtle border-warning',
            info: 'bg-info-subtle border-info'
        };
        return classes[type] || classes.info;
    }

    // Modal Management
    createModal(options = {}) {
        const modalId = 'modal-' + Date.now();
        const defaultOptions = {
            title: '',
            content: '',
            size: '',
            centered: true,
            scrollable: false,
            buttons: [
                { text: 'Close', class: 'btn-secondary', dismiss: true }
            ]
        };
        
        const config = { ...defaultOptions, ...options };
        
        const sizeClass = config.size ? `modal-${config.size}` : '';
        const centeredClass = config.centered ? 'modal-dialog-centered' : '';
        const scrollableClass = config.scrollable ? 'modal-dialog-scrollable' : '';
        
        const modalHTML = `
            <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog ${sizeClass} ${centeredClass} ${scrollableClass}">
                    <div class="modal-content">
                        ${config.title ? `
                        <div class="modal-header">
                            <h5 class="modal-title">${config.title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        ` : ''}
                        <div class="modal-body">
                            ${config.content}
                        </div>
                        ${config.buttons.length > 0 ? `
                        <div class="modal-footer">
                            ${config.buttons.map(btn => `
                                <button type="button" 
                                        class="btn ${btn.class || 'btn-secondary'}"
                                        ${btn.dismiss ? 'data-bs-dismiss="modal"' : ''}
                                        ${btn.onclick ? `onclick="${btn.onclick}"` : ''}>
                                    ${btn.icon ? `<i class="${btn.icon} me-2"></i>` : ''}
                                    ${btn.text}
                                </button>
                            `).join('')}
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        const modalElement = document.getElementById(modalId);
        const modal = new bootstrap.Modal(modalElement);
        
        // Remove modal from DOM when hidden
        modalElement.addEventListener('hidden.bs.modal', () => {
            modalElement.remove();
        });
        
        return { modal, element: modalElement };
    }

    // Confirmation Dialog
    confirm(message, title = 'Confirm Action', options = {}) {
        return new Promise((resolve) => {
            const defaultOptions = {
                confirmText: 'Confirm',
                cancelText: 'Cancel',
                confirmClass: 'btn-primary',
                cancelClass: 'btn-secondary',
                icon: 'fas fa-question-circle'
            };
            
            const config = { ...defaultOptions, ...options };
            
            const { modal } = this.createModal({
                title: title,
                content: `
                    <div class="text-center">
                        <i class="${config.icon} fa-3x text-warning mb-3"></i>
                        <p class="mb-0">${message}</p>
                    </div>
                `,
                centered: true,
                buttons: [
                    {
                        text: config.cancelText,
                        class: config.cancelClass,
                        dismiss: true,
                        onclick: () => resolve(false)
                    },
                    {
                        text: config.confirmText,
                        class: config.confirmClass,
                        onclick: () => {
                            modal.hide();
                            resolve(true);
                        }
                    }
                ]
            });
            
            modal.show();
        });
    }

    // Loading States Management
    showPageLoading(message = 'Loading...') {
        const loadingHTML = `
            <div id="pageLoading" class="page-loading-overlay">
                <div class="page-loading-content">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="loading-text">${message}</div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', loadingHTML);
        document.body.style.overflow = 'hidden';
    }

    hidePageLoading() {
        const loading = document.getElementById('pageLoading');
        if (loading) {
            loading.remove();
            document.body.style.overflow = '';
        }
    }

    // Skeleton Loading
    showSkeleton(container, type = 'text', options = {}) {
        if (typeof container === 'string') {
            container = document.querySelector(container);
        }
        
        if (!container) return;
        
        container.dataset.originalContent = container.innerHTML;
        
        let skeletonHTML = '';
        
        switch (type) {
            case 'text':
                const lines = options.lines || 3;
                for (let i = 0; i < lines; i++) {
                    const width = i === lines - 1 ? '60%' : '100%';
                    skeletonHTML += `<div class="skeleton skeleton-text" style="width: ${width}"></div>`;
                }
                break;
                
            case 'card':
                skeletonHTML = `
                    <div class="skeleton skeleton-title mb-3"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text" style="width: 70%"></div>
                `;
                break;
                
            case 'stats':
                skeletonHTML = `
                    <div class="d-flex align-items-center">
                        <div class="skeleton me-3" style="width: 3rem; height: 3rem; border-radius: 0.75rem;"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton skeleton-title mb-2"></div>
                            <div class="skeleton skeleton-text" style="width: 60%"></div>
                        </div>
                    </div>
                `;
                break;
                
            default:
                skeletonHTML = '<div class="skeleton" style="height: 100px;"></div>';
        }
        
        container.innerHTML = skeletonHTML;
        container.classList.add('skeleton-container');
    }

    hideSkeleton(container) {
        if (typeof container === 'string') {
            container = document.querySelector(container);
        }
        
        if (!container) return;
        
        if (container.dataset.originalContent) {
            container.innerHTML = container.dataset.originalContent;
            delete container.dataset.originalContent;
        }
        
        container.classList.remove('skeleton-container');
    }

    // Progress Indicators
    createProgressBar(container, options = {}) {
        if (typeof container === 'string') {
            container = document.querySelector(container);
        }
        
        if (!container) return;
        
        const defaultOptions = {
            value: 0,
            max: 100,
            animated: true,
            striped: false,
            color: 'primary',
            showLabel: true
        };
        
        const config = { ...defaultOptions, ...options };
        const percentage = Math.round((config.value / config.max) * 100);
        
        const progressHTML = `
            <div class="progress" style="height: ${config.height || '1rem'}">
                <div class="progress-bar ${config.animated ? 'progress-bar-animated' : ''} ${config.striped ? 'progress-bar-striped' : ''} bg-${config.color}"
                     role="progressbar"
                     style="width: ${percentage}%"
                     aria-valuenow="${config.value}"
                     aria-valuemin="0"
                     aria-valuemax="${config.max}">
                    ${config.showLabel ? `${percentage}%` : ''}
                </div>
            </div>
        `;
        
        container.innerHTML = progressHTML;
        
        return {
            update: (newValue) => {
                const newPercentage = Math.round((newValue / config.max) * 100);
                const progressBar = container.querySelector('.progress-bar');
                if (progressBar) {
                    progressBar.style.width = `${newPercentage}%`;
                    progressBar.setAttribute('aria-valuenow', newValue);
                    if (config.showLabel) {
                        progressBar.textContent = `${newPercentage}%`;
                    }
                }
            }
        };
    }

    // Smooth Scrolling
    smoothScrollTo(target, options = {}) {
        const defaultOptions = {
            behavior: 'smooth',
            block: 'start',
            inline: 'nearest'
        };
        
        const config = { ...defaultOptions, ...options };
        
        if (typeof target === 'string') {
            target = document.querySelector(target);
        }
        
        if (target) {
            target.scrollIntoView(config);
        }
    }

    // Lazy Loading
    initLazyLoading() {
        const lazyElements = document.querySelectorAll('[data-lazy]');
        
        if ('IntersectionObserver' in window) {
            const lazyObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        
                        if (element.tagName === 'IMG') {
                            element.src = element.dataset.lazy;
                        } else {
                            element.style.backgroundImage = `url(${element.dataset.lazy})`;
                        }
                        
                        element.classList.add('lazy-loaded');
                        lazyObserver.unobserve(element);
                    }
                });
            });
            
            lazyElements.forEach(element => {
                lazyObserver.observe(element);
            });
        } else {
            // Fallback for older browsers
            lazyElements.forEach(element => {
                if (element.tagName === 'IMG') {
                    element.src = element.dataset.lazy;
                } else {
                    element.style.backgroundImage = `url(${element.dataset.lazy})`;
                }
                element.classList.add('lazy-loaded');
            });
        }
    }

    // Auto-save functionality
    initAutoSave(formSelector, options = {}) {
        const form = document.querySelector(formSelector);
        if (!form) return;
        
        const defaultOptions = {
            interval: 30000, // 30 seconds
            storageKey: 'autosave_' + (form.id || 'form'),
            showIndicator: true
        };
        
        const config = { ...defaultOptions, ...options };
        let autoSaveTimer;
        
        const saveFormData = () => {
            const formData = new FormData(form);
            const data = {};
            
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            localStorage.setItem(config.storageKey, JSON.stringify({
                data: data,
                timestamp: Date.now()
            }));
            
            if (config.showIndicator) {
                this.showToast('Draft saved automatically', 'info', '', { delay: 2000 });
            }
        };
        
        const restoreFormData = () => {
            const saved = localStorage.getItem(config.storageKey);
            if (saved) {
                try {
                    const { data, timestamp } = JSON.parse(saved);
                    
                    // Only restore if saved within last 24 hours
                    if (Date.now() - timestamp < 24 * 60 * 60 * 1000) {
                        Object.keys(data).forEach(key => {
                            const field = form.querySelector(`[name="${key}"]`);
                            if (field) {
                                field.value = data[key];
                            }
                        });
                        
                        this.showToast('Draft restored', 'info', '', { delay: 3000 });
                    }
                } catch (e) {
                    console.warn('Failed to restore form data:', e);
                }
            }
        };
        
        // Restore data on page load
        restoreFormData();
        
        // Set up auto-save
        form.addEventListener('input', () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(saveFormData, config.interval);
        });
        
        // Clear auto-save on successful submit
        form.addEventListener('submit', () => {
            localStorage.removeItem(config.storageKey);
        });
        
        return {
            save: saveFormData,
            restore: restoreFormData,
            clear: () => localStorage.removeItem(config.storageKey)
        };
    }
}

// Enhanced global utility functions
window.showToast = function(message, type = 'info', title = '', options = {}) {
    if (window.app) {
        return window.app.createToast(message, type, title, options);
    }
};

window.showModal = function(options = {}) {
    if (window.app) {
        return window.app.createModal(options);
    }
};

window.confirmAction = function(message, title = 'Confirm Action', options = {}) {
    if (window.app) {
        return window.app.confirm(message, title, options);
    }
    return Promise.resolve(confirm(message));
};

window.showPageLoading = function(message = 'Loading...') {
    if (window.app) {
        window.app.showPageLoading(message);
    }
};

window.hidePageLoading = function() {
    if (window.app) {
        window.app.hidePageLoading();
    }
};

// Initialize enhanced features on DOM load
document.addEventListener('DOMContentLoaded', function() {
    if (window.app) {
        // Initialize lazy loading
        window.app.initLazyLoading();
        
        // Initialize auto-save for forms with data-autosave attribute
        document.querySelectorAll('form[data-autosave]').forEach(form => {
            window.app.initAutoSave(`#${form.id}`);
        });
    }
});