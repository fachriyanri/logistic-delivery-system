/**
 * Progressive Enhancement Helper
 * Provides fallbacks and enhancements based on browser capabilities
 */

class ProgressiveEnhancement {
    constructor() {
        this.features = this.detectFeatures();
        this.init();
    }

    detectFeatures() {
        return {
            // JavaScript features
            es6: this.supportsES6(),
            fetch: 'fetch' in window,
            promises: 'Promise' in window,
            intersectionObserver: 'IntersectionObserver' in window,
            
            // CSS features
            customProperties: this.supportsCustomProperties(),
            grid: this.supportsGrid(),
            flexbox: this.supportsFlexbox(),
            transforms: this.supportsTransforms(),
            transitions: this.supportsTransitions(),
            
            // Device capabilities
            touch: 'ontouchstart' in window,
            geolocation: 'geolocation' in navigator,
            camera: this.supportsCamera(),
            vibration: 'vibrate' in navigator,
            
            // Network
            connection: 'connection' in navigator,
            serviceWorker: 'serviceWorker' in navigator
        };
    }

    supportsES6() {
        try {
            new Function('(a = 0) => a');
            return true;
        } catch (err) {
            return false;
        }
    }

    supportsCustomProperties() {
        return window.CSS && CSS.supports && CSS.supports('color', 'var(--test)');
    }

    supportsGrid() {
        return CSS.supports('display', 'grid');
    }

    supportsFlexbox() {
        return CSS.supports('display', 'flex');
    }

    supportsTransforms() {
        return CSS.supports('transform', 'translateX(0)');
    }

    supportsTransitions() {
        return CSS.supports('transition', 'all 0.3s');
    }

    supportsCamera() {
        return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
    }

    init() {
        this.addFeatureClasses();
        this.setupFallbacks();
        this.enhanceBasedOnCapabilities();
    }

    addFeatureClasses() {
        const body = document.body;
        
        // Add feature support classes
        Object.keys(this.features).forEach(feature => {
            const className = this.features[feature] ? `supports-${feature}` : `no-${feature}`;
            body.classList.add(className);
        });
        
        // Add connection type if available
        if (this.features.connection) {
            const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            if (connection) {
                body.classList.add(`connection-${connection.effectiveType || 'unknown'}`);
            }
        }
    }

    setupFallbacks() {
        // Fetch fallback
        if (!this.features.fetch) {
            this.setupFetchFallback();
        }
        
        // Intersection Observer fallback
        if (!this.features.intersectionObserver) {
            this.setupIntersectionObserverFallback();
        }
        
        // CSS Grid fallback
        if (!this.features.grid) {
            this.setupGridFallback();
        }
        
        // Custom properties fallback
        if (!this.features.customProperties) {
            this.setupCustomPropertiesFallback();
        }
    }

    setupFetchFallback() {
        // Use XMLHttpRequest as fallback
        window.fetch = window.fetch || function(url, options = {}) {
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
                        statusText: xhr.statusText,
                        json: () => Promise.resolve(JSON.parse(xhr.responseText)),
                        text: () => Promise.resolve(xhr.responseText)
                    });
                };
                
                xhr.onerror = () => reject(new Error('Network error'));
                xhr.send(options.body);
            });
        };
    }

    setupIntersectionObserverFallback() {
        // Simple fallback for lazy loading
        const lazyElements = document.querySelectorAll('[data-lazy]');
        lazyElements.forEach(element => {
            // Load immediately as fallback
            if (element.tagName === 'IMG') {
                element.src = element.dataset.lazy;
            } else {
                element.style.backgroundImage = `url(${element.dataset.lazy})`;
            }
        });
    }

    setupGridFallback() {
        // Convert grid layouts to flexbox
        const gridContainers = document.querySelectorAll('.grid-container');
        gridContainers.forEach(container => {
            container.style.display = 'flex';
            container.style.flexWrap = 'wrap';
            
            const gridItems = container.children;
            Array.from(gridItems).forEach(item => {
                item.style.flex = '1 1 300px';
                item.style.margin = '0.5rem';
            });
        });
    }

    setupCustomPropertiesFallback() {
        // Define fallback values
        const fallbackStyles = `
            .no-custom-properties {
                color: #343a40;
                background-color: #ffffff;
            }
            .no-custom-properties .btn-primary {
                background-color: #1a2332;
                border-color: #1a2332;
            }
            .no-custom-properties .btn-secondary {
                background-color: #ff6b35;
                border-color: #ff6b35;
            }
        `;
        
        const style = document.createElement('style');
        style.textContent = fallbackStyles;
        document.head.appendChild(style);
    }

    enhanceBasedOnCapabilities() {
        // Touch enhancements
        if (this.features.touch) {
            this.enableTouchEnhancements();
        }
        
        // Camera enhancements
        if (this.features.camera) {
            this.enableCameraFeatures();
        }
        
        // Geolocation enhancements
        if (this.features.geolocation) {
            this.enableLocationFeatures();
        }
        
        // Connection-aware features
        if (this.features.connection) {
            this.enableConnectionAwareFeatures();
        }
        
        // Service Worker enhancements
        if (this.features.serviceWorker) {
            this.enableOfflineFeatures();
        }
    }

    enableTouchEnhancements() {
        // Add touch-friendly classes
        document.body.classList.add('touch-enabled');
        
        // Increase touch targets
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(btn => {
            if (!btn.style.minHeight) {
                btn.style.minHeight = '44px';
            }
            if (!btn.style.minWidth) {
                btn.style.minWidth = '44px';
            }
        });
        
        // Add swipe gestures to appropriate elements
        this.addSwipeGestures();
    }

    addSwipeGestures() {
        const swipeableElements = document.querySelectorAll('.swipeable');
        
        swipeableElements.forEach(element => {
            let startX, startY, startTime;
            
            element.addEventListener('touchstart', (e) => {
                const touch = e.touches[0];
                startX = touch.clientX;
                startY = touch.clientY;
                startTime = Date.now();
            }, { passive: true });
            
            element.addEventListener('touchend', (e) => {
                if (!startX || !startY) return;
                
                const touch = e.changedTouches[0];
                const endX = touch.clientX;
                const endY = touch.clientY;
                const endTime = Date.now();
                
                const deltaX = endX - startX;
                const deltaY = endY - startY;
                const deltaTime = endTime - startTime;
                
                if (Math.abs(deltaX) > Math.abs(deltaY) && 
                    Math.abs(deltaX) > 50 && 
                    deltaTime < 300) {
                    
                    const direction = deltaX > 0 ? 'right' : 'left';
                    this.handleSwipe(element, direction);
                }
                
                startX = startY = null;
            }, { passive: true });
        });
    }

    handleSwipe(element, direction) {
        const event = new CustomEvent('swipe', {
            detail: { direction, element }
        });
        element.dispatchEvent(event);
    }

    enableCameraFeatures() {
        // Add camera access buttons where appropriate
        const cameraButtons = document.querySelectorAll('[data-camera]');
        cameraButtons.forEach(button => {
            button.style.display = 'inline-block';
            button.addEventListener('click', this.handleCameraAccess.bind(this));
        });
    }

    async handleCameraAccess(event) {
        const button = event.target;
        const targetInput = document.querySelector(button.dataset.camera);
        
        if (!targetInput) return;
        
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' } 
            });
            
            // Create video element for camera preview
            const video = document.createElement('video');
            video.srcObject = stream;
            video.play();
            
            // Add capture functionality
            this.setupCameraCapture(video, targetInput, stream);
            
        } catch (error) {
            console.warn('Camera access denied:', error);
            // Fallback to file input
            targetInput.click();
        }
    }

    setupCameraCapture(video, input, stream) {
        // Create modal for camera interface
        const modal = document.createElement('div');
        modal.className = 'camera-modal';
        modal.innerHTML = `
            <div class="camera-interface">
                <div class="camera-preview"></div>
                <div class="camera-controls">
                    <button class="btn btn-secondary" id="cancelCamera">Cancel</button>
                    <button class="btn btn-primary" id="capturePhoto">Capture</button>
                </div>
            </div>
        `;
        
        modal.querySelector('.camera-preview').appendChild(video);
        document.body.appendChild(modal);
        
        // Handle capture
        modal.querySelector('#capturePhoto').addEventListener('click', () => {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);
            
            canvas.toBlob(blob => {
                const file = new File([blob], 'camera-capture.jpg', { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
                
                // Trigger change event
                input.dispatchEvent(new Event('change', { bubbles: true }));
                
                this.closeCameraModal(modal, stream);
            }, 'image/jpeg', 0.8);
        });
        
        // Handle cancel
        modal.querySelector('#cancelCamera').addEventListener('click', () => {
            this.closeCameraModal(modal, stream);
        });
    }

    closeCameraModal(modal, stream) {
        stream.getTracks().forEach(track => track.stop());
        modal.remove();
    }

    enableLocationFeatures() {
        const locationButtons = document.querySelectorAll('[data-location]');
        locationButtons.forEach(button => {
            button.style.display = 'inline-block';
            button.addEventListener('click', this.handleLocationAccess.bind(this));
        });
    }

    handleLocationAccess(event) {
        const button = event.target;
        const targetInput = document.querySelector(button.dataset.location);
        
        if (!targetInput) return;
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                targetInput.value = `${latitude}, ${longitude}`;
                targetInput.dispatchEvent(new Event('change', { bubbles: true }));
            },
            (error) => {
                console.warn('Location access denied:', error);
                alert('Location access is required for this feature.');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    enableConnectionAwareFeatures() {
        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        
        if (connection) {
            // Adjust features based on connection speed
            if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                this.enableLowBandwidthMode();
            }
            
            // Listen for connection changes
            connection.addEventListener('change', () => {
                this.handleConnectionChange(connection);
            });
        }
    }

    enableLowBandwidthMode() {
        document.body.classList.add('low-bandwidth');
        
        // Disable animations
        const style = document.createElement('style');
        style.textContent = `
            .low-bandwidth * {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        `;
        document.head.appendChild(style);
        
        // Lazy load images more aggressively
        const images = document.querySelectorAll('img[data-lazy]');
        images.forEach(img => {
            img.loading = 'lazy';
        });
    }

    handleConnectionChange(connection) {
        if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
            this.enableLowBandwidthMode();
        } else {
            document.body.classList.remove('low-bandwidth');
        }
    }

    enableOfflineFeatures() {
        // Register service worker for offline functionality
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('Service Worker registered:', registration);
                })
                .catch(error => {
                    console.log('Service Worker registration failed:', error);
                });
        }
        
        // Handle online/offline events
        window.addEventListener('online', this.handleOnline.bind(this));
        window.addEventListener('offline', this.handleOffline.bind(this));
    }

    handleOnline() {
        document.body.classList.remove('offline');
        this.showConnectionStatus('Back online', 'success');
    }

    handleOffline() {
        document.body.classList.add('offline');
        this.showConnectionStatus('You are offline', 'warning');
    }

    showConnectionStatus(message, type) {
        if (window.app && window.app.showToast) {
            window.app.showToast(message, type);
        }
    }
}

// Initialize progressive enhancement when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.progressiveEnhancement = new ProgressiveEnhancement();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProgressiveEnhancement;
}