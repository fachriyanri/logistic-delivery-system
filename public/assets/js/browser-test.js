/**
 * Browser Testing Helper
 * Provides automated testing for cross-browser compatibility
 */

class BrowserTester {
    constructor() {
        this.testResults = {};
        this.init();
    }

    init() {
        this.runCompatibilityTests();
        this.displayResults();
    }

    runCompatibilityTests() {
        console.log('Running browser compatibility tests...');
        
        this.testResults = {
            javascript: this.testJavaScriptFeatures(),
            css: this.testCSSFeatures(),
            html: this.testHTMLFeatures(),
            performance: this.testPerformanceFeatures(),
            accessibility: this.testAccessibilityFeatures()
        };
    }

    testJavaScriptFeatures() {
        const tests = {
            es6ArrowFunctions: this.testES6ArrowFunctions(),
            es6Classes: this.testES6Classes(),
            es6Modules: this.testES6Modules(),
            fetch: this.testFetch(),
            promises: this.testPromises(),
            asyncAwait: this.testAsyncAwait(),
            intersectionObserver: this.testIntersectionObserver(),
            mutationObserver: this.testMutationObserver(),
            webWorkers: this.testWebWorkers(),
            localStorage: this.testLocalStorage(),
            sessionStorage: this.testSessionStorage(),
            indexedDB: this.testIndexedDB()
        };

        return tests;
    }

    testES6ArrowFunctions() {
        try {
            new Function('() => {}');
            return { supported: true, message: 'ES6 Arrow Functions supported' };
        } catch (e) {
            return { supported: false, message: 'ES6 Arrow Functions not supported', error: e.message };
        }
    }

    testES6Classes() {
        try {
            new Function('class Test {}');
            return { supported: true, message: 'ES6 Classes supported' };
        } catch (e) {
            return { supported: false, message: 'ES6 Classes not supported', error: e.message };
        }
    }

    testES6Modules() {
        const supported = 'import' in document.createElement('script');
        return { 
            supported, 
            message: supported ? 'ES6 Modules supported' : 'ES6 Modules not supported' 
        };
    }

    testFetch() {
        const supported = 'fetch' in window;
        return { 
            supported, 
            message: supported ? 'Fetch API supported' : 'Fetch API not supported' 
        };
    }

    testPromises() {
        const supported = 'Promise' in window;
        return { 
            supported, 
            message: supported ? 'Promises supported' : 'Promises not supported' 
        };
    }

    testAsyncAwait() {
        try {
            new Function('async function test() { await Promise.resolve(); }');
            return { supported: true, message: 'Async/Await supported' };
        } catch (e) {
            return { supported: false, message: 'Async/Await not supported', error: e.message };
        }
    }

    testIntersectionObserver() {
        const supported = 'IntersectionObserver' in window;
        return { 
            supported, 
            message: supported ? 'Intersection Observer supported' : 'Intersection Observer not supported' 
        };
    }

    testMutationObserver() {
        const supported = 'MutationObserver' in window;
        return { 
            supported, 
            message: supported ? 'Mutation Observer supported' : 'Mutation Observer not supported' 
        };
    }

    testWebWorkers() {
        const supported = 'Worker' in window;
        return { 
            supported, 
            message: supported ? 'Web Workers supported' : 'Web Workers not supported' 
        };
    }

    testLocalStorage() {
        try {
            const test = 'test';
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return { supported: true, message: 'Local Storage supported' };
        } catch (e) {
            return { supported: false, message: 'Local Storage not supported', error: e.message };
        }
    }

    testSessionStorage() {
        try {
            const test = 'test';
            sessionStorage.setItem(test, test);
            sessionStorage.removeItem(test);
            return { supported: true, message: 'Session Storage supported' };
        } catch (e) {
            return { supported: false, message: 'Session Storage not supported', error: e.message };
        }
    }

    testIndexedDB() {
        const supported = 'indexedDB' in window;
        return { 
            supported, 
            message: supported ? 'IndexedDB supported' : 'IndexedDB not supported' 
        };
    }

    testCSSFeatures() {
        const tests = {
            customProperties: this.testCSSCustomProperties(),
            grid: this.testCSSGrid(),
            flexbox: this.testCSSFlexbox(),
            transforms: this.testCSSTransforms(),
            transitions: this.testCSSTransitions(),
            animations: this.testCSSAnimations(),
            calc: this.testCSSCalc(),
            viewport: this.testCSSViewport(),
            objectFit: this.testCSSObjectFit(),
            sticky: this.testCSSSticky()
        };

        return tests;
    }

    testCSSCustomProperties() {
        const supported = window.CSS && CSS.supports && CSS.supports('color', 'var(--test)');
        return { 
            supported, 
            message: supported ? 'CSS Custom Properties supported' : 'CSS Custom Properties not supported' 
        };
    }

    testCSSGrid() {
        const supported = CSS.supports('display', 'grid');
        return { 
            supported, 
            message: supported ? 'CSS Grid supported' : 'CSS Grid not supported' 
        };
    }

    testCSSFlexbox() {
        const supported = CSS.supports('display', 'flex');
        return { 
            supported, 
            message: supported ? 'CSS Flexbox supported' : 'CSS Flexbox not supported' 
        };
    }

    testCSSTransforms() {
        const supported = CSS.supports('transform', 'translateX(0)');
        return { 
            supported, 
            message: supported ? 'CSS Transforms supported' : 'CSS Transforms not supported' 
        };
    }

    testCSSTransitions() {
        const supported = CSS.supports('transition', 'all 0.3s');
        return { 
            supported, 
            message: supported ? 'CSS Transitions supported' : 'CSS Transitions not supported' 
        };
    }

    testCSSAnimations() {
        const supported = CSS.supports('animation', 'test 1s');
        return { 
            supported, 
            message: supported ? 'CSS Animations supported' : 'CSS Animations not supported' 
        };
    }

    testCSSCalc() {
        const supported = CSS.supports('width', 'calc(100% - 10px)');
        return { 
            supported, 
            message: supported ? 'CSS calc() supported' : 'CSS calc() not supported' 
        };
    }

    testCSSViewport() {
        const supported = CSS.supports('width', '100vw');
        return { 
            supported, 
            message: supported ? 'CSS Viewport units supported' : 'CSS Viewport units not supported' 
        };
    }

    testCSSObjectFit() {
        const supported = CSS.supports('object-fit', 'cover');
        return { 
            supported, 
            message: supported ? 'CSS object-fit supported' : 'CSS object-fit not supported' 
        };
    }

    testCSSSticky() {
        const supported = CSS.supports('position', 'sticky');
        return { 
            supported, 
            message: supported ? 'CSS position: sticky supported' : 'CSS position: sticky not supported' 
        };
    }

    testHTMLFeatures() {
        const tests = {
            html5Semantic: this.testHTML5Semantic(),
            formValidation: this.testFormValidation(),
            inputTypes: this.testInputTypes(),
            canvas: this.testCanvas(),
            svg: this.testSVG(),
            video: this.testVideo(),
            audio: this.testAudio(),
            webgl: this.testWebGL()
        };

        return tests;
    }

    testHTML5Semantic() {
        const elements = ['article', 'section', 'nav', 'header', 'footer', 'aside', 'main'];
        const supported = elements.every(tag => document.createElement(tag).toString() !== '[object HTMLUnknownElement]');
        return { 
            supported, 
            message: supported ? 'HTML5 Semantic elements supported' : 'HTML5 Semantic elements not supported' 
        };
    }

    testFormValidation() {
        const input = document.createElement('input');
        input.type = 'email';
        input.required = true;
        const supported = typeof input.checkValidity === 'function';
        return { 
            supported, 
            message: supported ? 'HTML5 Form Validation supported' : 'HTML5 Form Validation not supported' 
        };
    }

    testInputTypes() {
        const types = ['email', 'url', 'number', 'range', 'date', 'color'];
        const input = document.createElement('input');
        const supportedTypes = types.filter(type => {
            input.type = type;
            return input.type === type;
        });
        
        return { 
            supported: supportedTypes.length === types.length, 
            message: `HTML5 Input Types: ${supportedTypes.length}/${types.length} supported`,
            details: supportedTypes
        };
    }

    testCanvas() {
        const canvas = document.createElement('canvas');
        const supported = !!(canvas.getContext && canvas.getContext('2d'));
        return { 
            supported, 
            message: supported ? 'HTML5 Canvas supported' : 'HTML5 Canvas not supported' 
        };
    }

    testSVG() {
        const supported = !!(document.createElementNS && document.createElementNS('http://www.w3.org/2000/svg', 'svg').createSVGRect);
        return { 
            supported, 
            message: supported ? 'SVG supported' : 'SVG not supported' 
        };
    }

    testVideo() {
        const video = document.createElement('video');
        const supported = !!(video.canPlayType);
        return { 
            supported, 
            message: supported ? 'HTML5 Video supported' : 'HTML5 Video not supported' 
        };
    }

    testAudio() {
        const audio = document.createElement('audio');
        const supported = !!(audio.canPlayType);
        return { 
            supported, 
            message: supported ? 'HTML5 Audio supported' : 'HTML5 Audio not supported' 
        };
    }

    testWebGL() {
        const canvas = document.createElement('canvas');
        const supported = !!(canvas.getContext && (canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
        return { 
            supported, 
            message: supported ? 'WebGL supported' : 'WebGL not supported' 
        };
    }

    testPerformanceFeatures() {
        const tests = {
            performanceAPI: this.testPerformanceAPI(),
            requestAnimationFrame: this.testRequestAnimationFrame(),
            pageVisibility: this.testPageVisibility(),
            networkInformation: this.testNetworkInformation()
        };

        return tests;
    }

    testPerformanceAPI() {
        const supported = 'performance' in window && 'now' in performance;
        return { 
            supported, 
            message: supported ? 'Performance API supported' : 'Performance API not supported' 
        };
    }

    testRequestAnimationFrame() {
        const supported = 'requestAnimationFrame' in window;
        return { 
            supported, 
            message: supported ? 'requestAnimationFrame supported' : 'requestAnimationFrame not supported' 
        };
    }

    testPageVisibility() {
        const supported = 'visibilityState' in document;
        return { 
            supported, 
            message: supported ? 'Page Visibility API supported' : 'Page Visibility API not supported' 
        };
    }

    testNetworkInformation() {
        const supported = 'connection' in navigator;
        return { 
            supported, 
            message: supported ? 'Network Information API supported' : 'Network Information API not supported' 
        };
    }

    testAccessibilityFeatures() {
        const tests = {
            ariaSupport: this.testARIASupport(),
            focusManagement: this.testFocusManagement(),
            screenReader: this.testScreenReaderSupport()
        };

        return tests;
    }

    testARIASupport() {
        const element = document.createElement('div');
        element.setAttribute('aria-label', 'test');
        const supported = element.getAttribute('aria-label') === 'test';
        return { 
            supported, 
            message: supported ? 'ARIA attributes supported' : 'ARIA attributes not supported' 
        };
    }

    testFocusManagement() {
        const element = document.createElement('button');
        document.body.appendChild(element);
        element.focus();
        const supported = document.activeElement === element;
        document.body.removeChild(element);
        return { 
            supported, 
            message: supported ? 'Focus management supported' : 'Focus management not supported' 
        };
    }

    testScreenReaderSupport() {
        // Basic test for screen reader APIs
        const supported = 'speechSynthesis' in window;
        return { 
            supported, 
            message: supported ? 'Speech Synthesis API supported' : 'Speech Synthesis API not supported' 
        };
    }

    displayResults() {
        console.group('Browser Compatibility Test Results');
        
        Object.keys(this.testResults).forEach(category => {
            console.group(`${category.toUpperCase()} Tests`);
            
            const tests = this.testResults[category];
            Object.keys(tests).forEach(testName => {
                const result = tests[testName];
                const status = result.supported ? '✅' : '❌';
                console.log(`${status} ${testName}: ${result.message}`);
                
                if (result.error) {
                    console.warn(`   Error: ${result.error}`);
                }
                
                if (result.details) {
                    console.log(`   Details:`, result.details);
                }
            });
            
            console.groupEnd();
        });
        
        console.groupEnd();
        
        // Generate summary
        this.generateSummary();
    }

    generateSummary() {
        const summary = {
            total: 0,
            supported: 0,
            unsupported: 0,
            categories: {}
        };

        Object.keys(this.testResults).forEach(category => {
            const tests = this.testResults[category];
            const categoryStats = { total: 0, supported: 0, unsupported: 0 };
            
            Object.keys(tests).forEach(testName => {
                const result = tests[testName];
                categoryStats.total++;
                summary.total++;
                
                if (result.supported) {
                    categoryStats.supported++;
                    summary.supported++;
                } else {
                    categoryStats.unsupported++;
                    summary.unsupported++;
                }
            });
            
            summary.categories[category] = categoryStats;
        });

        console.log('\n📊 COMPATIBILITY SUMMARY');
        console.log(`Overall: ${summary.supported}/${summary.total} features supported (${Math.round(summary.supported/summary.total*100)}%)`);
        
        Object.keys(summary.categories).forEach(category => {
            const stats = summary.categories[category];
            const percentage = Math.round(stats.supported/stats.total*100);
            console.log(`${category}: ${stats.supported}/${stats.total} (${percentage}%)`);
        });

        // Store results for potential reporting
        window.browserCompatibilityResults = {
            summary,
            details: this.testResults,
            userAgent: navigator.userAgent,
            timestamp: new Date().toISOString()
        };
    }

    exportResults() {
        const results = window.browserCompatibilityResults;
        const blob = new Blob([JSON.stringify(results, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.href = url;
        a.download = `browser-compatibility-${Date.now()}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
}

// Auto-run tests in development mode
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    document.addEventListener('DOMContentLoaded', () => {
        window.browserTester = new BrowserTester();
        
        // Add export button for developers
        const exportBtn = document.createElement('button');
        exportBtn.textContent = 'Export Compatibility Results';
        exportBtn.style.cssText = `
            position: fixed;
            bottom: 10px;
            left: 10px;
            z-index: 9999;
            padding: 5px 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            font-size: 12px;
            cursor: pointer;
        `;
        exportBtn.onclick = () => window.browserTester.exportResults();
        document.body.appendChild(exportBtn);
    });
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BrowserTester;
}