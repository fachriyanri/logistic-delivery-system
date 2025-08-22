<?php

if (!function_exists('optimized_css')) {
    /**
     * Load optimized CSS files
     */
    function optimized_css(string $group = 'app'): string
    {
        $config = config('Assets');
        $files = $config->cssFiles[$group] ?? [];
        
        if (empty($files)) {
            return '';
        }
        
        $html = '';
        
        if ($config->minifyAssets && count($files) > 1) {
            // Combine and minify CSS files
            $combinedFile = combine_css_files($files, $group);
            $html .= '<link rel="stylesheet" href="' . base_url($combinedFile) . '">';
        } else {
            // Load individual files
            foreach ($files as $file) {
                $version = $config->enableVersioning ? '?v=' . get_file_version($file) : '';
                $html .= '<link rel="stylesheet" href="' . base_url($file . $version) . '">' . "\n";
            }
        }
        
        return $html;
    }
}

if (!function_exists('optimized_js')) {
    /**
     * Load optimized JavaScript files
     */
    function optimized_js(string $group = 'app'): string
    {
        $config = config('Assets');
        $files = $config->jsFiles[$group] ?? [];
        
        if (empty($files)) {
            return '';
        }
        
        $html = '';
        
        if ($config->minifyAssets && count($files) > 1) {
            // Combine and minify JS files
            $combinedFile = combine_js_files($files, $group);
            $html .= '<script src="' . base_url($combinedFile) . '"></script>';
        } else {
            // Load individual files
            foreach ($files as $file) {
                $version = $config->enableVersioning ? '?v=' . get_file_version($file) : '';
                $html .= '<script src="' . base_url($file . $version) . '"></script>' . "\n";
            }
        }
        
        return $html;
    }
}

if (!function_exists('preload_resources')) {
    /**
     * Generate preload links for critical resources
     */
    function preload_resources(): string
    {
        $config = config('Assets');
        $html = '';
        
        // Preload CSS
        foreach ($config->preloadResources['css'] ?? [] as $file) {
            $html .= '<link rel="preload" href="' . base_url($file) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
        }
        
        // Preload JS
        foreach ($config->preloadResources['js'] ?? [] as $file) {
            $html .= '<link rel="preload" href="' . base_url($file) . '" as="script">' . "\n";
        }
        
        // Preload fonts
        foreach ($config->preloadResources['fonts'] ?? [] as $file) {
            $html .= '<link rel="preload" href="' . base_url($file) . '" as="font" type="font/woff2" crossorigin>' . "\n";
        }
        
        return $html;
    }
}

if (!function_exists('lazy_image')) {
    /**
     * Generate lazy-loaded image tag
     */
    function lazy_image(string $src, string $alt = '', array $attributes = []): string
    {
        $config = config('Assets');
        
        if (!$config->lazyLoad['images']) {
            $attrs = array_merge(['src' => base_url($src), 'alt' => $alt], $attributes);
            return '<img ' . stringify_attributes($attrs) . '>';
        }
        
        // Generate placeholder (1x1 transparent pixel)
        $placeholder = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"%3E%3C/svg%3E';
        
        $attrs = array_merge([
            'src' => $placeholder,
            'data-src' => base_url($src),
            'alt' => $alt,
            'loading' => 'lazy',
            'class' => 'lazy-image'
        ], $attributes);
        
        return '<img ' . stringify_attributes($attrs) . '>';
    }
}

if (!function_exists('responsive_image')) {
    /**
     * Generate responsive image with multiple sizes
     */
    function responsive_image(string $src, string $alt = '', array $sizes = []): string
    {
        $basePath = pathinfo($src, PATHINFO_DIRNAME);
        $filename = pathinfo($src, PATHINFO_FILENAME);
        $extension = pathinfo($src, PATHINFO_EXTENSION);
        
        $srcset = [];
        
        if (empty($sizes)) {
            $sizes = [480, 768, 1024, 1200];
        }
        
        foreach ($sizes as $size) {
            $resizedFile = "{$basePath}/{$filename}_{$size}w.{$extension}";
            if (file_exists(FCPATH . $resizedFile)) {
                $srcset[] = base_url($resizedFile) . " {$size}w";
            }
        }
        
        $attrs = [
            'src' => base_url($src),
            'alt' => $alt,
            'loading' => 'lazy'
        ];
        
        if (!empty($srcset)) {
            $attrs['srcset'] = implode(', ', $srcset);
            $attrs['sizes'] = '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw';
        }
        
        return '<img ' . stringify_attributes($attrs) . '>';
    }
}

if (!function_exists('combine_css_files')) {
    /**
     * Combine and minify CSS files
     */
    function combine_css_files(array $files, string $group): string
    {
        $cacheDir = WRITEPATH . 'cache/assets/';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        $cacheFile = $cacheDir . "combined_{$group}.css";
        $cacheTime = file_exists($cacheFile) ? filemtime($cacheFile) : 0;
        
        // Check if any source file is newer than cache
        $needsUpdate = false;
        foreach ($files as $file) {
            $filePath = FCPATH . $file;
            if (file_exists($filePath) && filemtime($filePath) > $cacheTime) {
                $needsUpdate = true;
                break;
            }
        }
        
        if ($needsUpdate || !file_exists($cacheFile)) {
            $combinedContent = '';
            
            foreach ($files as $file) {
                $filePath = FCPATH . $file;
                if (file_exists($filePath)) {
                    $content = file_get_contents($filePath);
                    $combinedContent .= minify_css($content) . "\n";
                }
            }
            
            file_put_contents($cacheFile, $combinedContent);
        }
        
        return 'writable/cache/assets/combined_' . $group . '.css';
    }
}

if (!function_exists('combine_js_files')) {
    /**
     * Combine and minify JavaScript files
     */
    function combine_js_files(array $files, string $group): string
    {
        $cacheDir = WRITEPATH . 'cache/assets/';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        $cacheFile = $cacheDir . "combined_{$group}.js";
        $cacheTime = file_exists($cacheFile) ? filemtime($cacheFile) : 0;
        
        // Check if any source file is newer than cache
        $needsUpdate = false;
        foreach ($files as $file) {
            $filePath = FCPATH . $file;
            if (file_exists($filePath) && filemtime($filePath) > $cacheTime) {
                $needsUpdate = true;
                break;
            }
        }
        
        if ($needsUpdate || !file_exists($cacheFile)) {
            $combinedContent = '';
            
            foreach ($files as $file) {
                $filePath = FCPATH . $file;
                if (file_exists($filePath)) {
                    $content = file_get_contents($filePath);
                    $combinedContent .= minify_js($content) . ";\n";
                }
            }
            
            file_put_contents($cacheFile, $combinedContent);
        }
        
        return 'writable/cache/assets/combined_' . $group . '.js';
    }
}

if (!function_exists('minify_css')) {
    /**
     * Simple CSS minification
     */
    function minify_css(string $css): string
    {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove unnecessary whitespace
        $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/\s*([{}:;,>+~])\s*/', '$1', $css);
        
        return trim($css);
    }
}

if (!function_exists('minify_js')) {
    /**
     * Simple JavaScript minification
     */
    function minify_js(string $js): string
    {
        // Remove single-line comments (but preserve URLs)
        $js = preg_replace('/(?<!:)\/\/.*$/m', '', $js);
        
        // Remove multi-line comments
        $js = preg_replace('/\/\*[\s\S]*?\*\//', '', $js);
        
        // Remove unnecessary whitespace
        $js = preg_replace('/\s+/', ' ', $js);
        $js = preg_replace('/\s*([{}:;,=()[\]<>!&|+-])\s*/', '$1', $js);
        
        return trim($js);
    }
}

if (!function_exists('get_file_version')) {
    /**
     * Get file version for cache busting
     */
    function get_file_version(string $file): string
    {
        $filePath = FCPATH . $file;
        
        if (file_exists($filePath)) {
            return substr(md5_file($filePath), 0, 8);
        }
        
        return '1';
    }
}

if (!function_exists('critical_css')) {
    /**
     * Inline critical CSS for above-the-fold content
     */
    function critical_css(): string
    {
        $criticalCssFile = FCPATH . 'assets/css/critical.css';
        
        if (file_exists($criticalCssFile)) {
            $css = file_get_contents($criticalCssFile);
            return '<style>' . minify_css($css) . '</style>';
        }
        
        return '';
    }
}

if (!function_exists('defer_js')) {
    /**
     * Load JavaScript with defer attribute
     */
    function defer_js(string $src): string
    {
        $version = config('Assets')->enableVersioning ? '?v=' . get_file_version($src) : '';
        return '<script defer src="' . base_url($src . $version) . '"></script>';
    }
}

if (!function_exists('async_js')) {
    /**
     * Load JavaScript with async attribute
     */
    function async_js(string $src): string
    {
        $version = config('Assets')->enableVersioning ? '?v=' . get_file_version($src) : '';
        return '<script async src="' . base_url($src . $version) . '"></script>';
    }
}