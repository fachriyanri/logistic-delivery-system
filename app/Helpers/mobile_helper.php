<?php

/**
 * Mobile Helper Functions
 * Provides utilities for mobile-responsive features
 */

if (!function_exists('is_mobile_device')) {
    /**
     * Detect if the current request is from a mobile device
     */
    function is_mobile_device(): bool
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $mobileKeywords = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 
            'BlackBerry', 'Windows Phone', 'Opera Mini'
        ];
        
        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('get_viewport_meta')) {
    /**
     * Generate viewport meta tag for mobile optimization
     */
    function get_viewport_meta(): string
    {
        return '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">';
    }
}

if (!function_exists('mobile_breakpoint_class')) {
    /**
     * Generate responsive classes based on breakpoint
     */
    function mobile_breakpoint_class(string $baseClass, array $breakpoints = []): string
    {
        $classes = [$baseClass];
        
        foreach ($breakpoints as $breakpoint => $class) {
            $classes[] = "{$breakpoint}-{$class}";
        }
        
        return implode(' ', $classes);
    }
}

if (!function_exists('touch_friendly_button')) {
    /**
     * Generate touch-friendly button attributes
     */
    function touch_friendly_button(array $options = []): array
    {
        $defaults = [
            'min-height' => '44px',
            'min-width' => '44px',
            'padding' => '0.75rem 1rem'
        ];
        
        return array_merge($defaults, $options);
    }
}

if (!function_exists('mobile_table_to_cards')) {
    /**
     * Convert table data to mobile card format
     */
    function mobile_table_to_cards(array $data, array $headers): array
    {
        $cards = [];
        
        foreach ($data as $row) {
            $card = [];
            foreach ($headers as $index => $header) {
                if (isset($row[$index])) {
                    $card[] = [
                        'label' => $header,
                        'value' => $row[$index]
                    ];
                }
            }
            $cards[] = $card;
        }
        
        return $cards;
    }
}

if (!function_exists('generate_mobile_nav_items')) {
    /**
     * Generate mobile navigation items based on user role
     */
    function generate_mobile_nav_items(int $userLevel): array
    {
        $items = [
            [
                'title' => 'Dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'url' => base_url('/dashboard'),
                'active' => uri_string() === 'dashboard'
            ]
        ];
        
        // Master Data (Admin & Gudang)
        if ($userLevel == 1 || $userLevel == 3) {
            $items[] = [
                'section' => 'Master Data'
            ];
            
            $items[] = [
                'title' => 'Categories',
                'icon' => 'fas fa-tags',
                'url' => base_url('/kategori'),
                'active' => strpos(uri_string(), 'kategori') !== false
            ];
            
            $items[] = [
                'title' => 'Items',
                'icon' => 'fas fa-boxes',
                'url' => base_url('/barang'),
                'active' => strpos(uri_string(), 'barang') !== false
            ];
            
            $items[] = [
                'title' => 'Couriers',
                'icon' => 'fas fa-motorcycle',
                'url' => base_url('/kurir'),
                'active' => strpos(uri_string(), 'kurir') !== false
            ];
        }
        
        // Customer Management (Admin & Finance)
        if ($userLevel == 1 || $userLevel == 2) {
            $items[] = [
                'title' => 'Customers',
                'icon' => 'fas fa-users',
                'url' => base_url('/pelanggan'),
                'active' => strpos(uri_string(), 'pelanggan') !== false
            ];
        }
        
        // Shipping
        $items[] = [
            'section' => 'Shipping'
        ];
        
        $items[] = [
            'title' => 'Shipments',
            'icon' => 'fas fa-shipping-fast',
            'url' => base_url('/pengiriman'),
            'active' => strpos(uri_string(), 'pengiriman') !== false
        ];
        
        // Reports (Admin & Finance)
        if ($userLevel == 1 || $userLevel == 2) {
            $items[] = [
                'section' => 'Reports'
            ];
            
            $items[] = [
                'title' => 'Shipping Reports',
                'icon' => 'fas fa-chart-bar',
                'url' => base_url('/laporan'),
                'active' => strpos(uri_string(), 'laporan') !== false
            ];
        }
        
        // Administration (Admin only)
        if ($userLevel == 1) {
            $items[] = [
                'section' => 'Administration'
            ];
            
            $items[] = [
                'title' => 'User Management',
                'icon' => 'fas fa-user-cog',
                'url' => base_url('/users'),
                'active' => strpos(uri_string(), 'users') !== false
            ];
        }
        
        return $items;
    }
}

if (!function_exists('mobile_form_layout')) {
    /**
     * Generate mobile-optimized form layout
     */
    function mobile_form_layout(array $fields): string
    {
        $html = '<div class="mobile-form-container">';
        
        foreach ($fields as $field) {
            $html .= '<div class="mobile-form-group">';
            
            if (isset($field['label'])) {
                $html .= '<label class="form-label">' . esc($field['label']) . '</label>';
            }
            
            switch ($field['type']) {
                case 'text':
                case 'email':
                case 'password':
                    $html .= '<input type="' . $field['type'] . '" class="form-control" ';
                    $html .= 'name="' . $field['name'] . '" ';
                    $html .= isset($field['required']) && $field['required'] ? 'required ' : '';
                    $html .= 'value="' . esc($field['value'] ?? '') . '">';
                    break;
                    
                case 'select':
                    $html .= '<select class="form-select" name="' . $field['name'] . '">';
                    foreach ($field['options'] as $value => $text) {
                        $selected = ($field['value'] ?? '') == $value ? 'selected' : '';
                        $html .= '<option value="' . $value . '" ' . $selected . '>' . esc($text) . '</option>';
                    }
                    $html .= '</select>';
                    break;
                    
                case 'textarea':
                    $html .= '<textarea class="form-control" name="' . $field['name'] . '" rows="3">';
                    $html .= esc($field['value'] ?? '');
                    $html .= '</textarea>';
                    break;
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('add_mobile_meta_tags')) {
    /**
     * Add mobile-specific meta tags to the page
     */
    function add_mobile_meta_tags(): string
    {
        $tags = [
            '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">',
            '<meta name="mobile-web-app-capable" content="yes">',
            '<meta name="apple-mobile-web-app-capable" content="yes">',
            '<meta name="apple-mobile-web-app-status-bar-style" content="default">',
            '<meta name="theme-color" content="#1a2332">',
            '<meta name="msapplication-navbutton-color" content="#1a2332">',
            '<meta name="apple-mobile-web-app-title" content="' . APP_NAME . '">',
            '<link rel="apple-touch-icon" href="' . base_url('assets/images/puninar_logo.webp') . '">'
        ];
        
        return implode("\n", $tags);
    }
}

if (!function_exists('mobile_optimized_image')) {
    /**
     * Generate mobile-optimized image tag with lazy loading
     */
    function mobile_optimized_image(string $src, string $alt = '', array $options = []): string
    {
        $defaults = [
            'loading' => 'lazy',
            'class' => 'img-fluid',
            'style' => 'max-width: 100%; height: auto;'
        ];
        
        $attributes = array_merge($defaults, $options);
        
        $html = '<img src="' . $src . '" alt="' . esc($alt) . '"';
        
        foreach ($attributes as $key => $value) {
            $html .= ' ' . $key . '="' . $value . '"';
        }
        
        $html .= '>';
        
        return $html;
    }
}