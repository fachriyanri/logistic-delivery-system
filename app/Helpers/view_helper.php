<?php

if (!function_exists('component')) {
    /**
     * Render a view component
     * 
     * @param string $component Component name
     * @param array $data Component data
     * @return string Rendered component HTML
     */
    function component(string $component, array $data = []): string
    {
        return view("components/{$component}", $data);
    }
}

if (!function_exists('card')) {
    /**
     * Render a card component
     * 
     * @param string $title Card title
     * @param string $content Card content
     * @param array $options Additional options
     * @return string Rendered card HTML
     */
    function card(string $title = '', string $content = '', array $options = []): string
    {
        return component('card', array_merge([
            'title' => $title,
            'content' => $content
        ], $options));
    }
}

if (!function_exists('button')) {
    /**
     * Render a button component
     * 
     * @param string $text Button text
     * @param array $options Button options
     * @return string Rendered button HTML
     */
    function button(string $text, array $options = []): string
    {
        return component('button', array_merge([
            'text' => $text
        ], $options));
    }
}

if (!function_exists('badge')) {
    /**
     * Render a badge component
     * 
     * @param string $text Badge text
     * @param string $variant Badge variant
     * @param array $options Additional options
     * @return string Rendered badge HTML
     */
    function badge(string $text, string $variant = 'primary', array $options = []): string
    {
        return component('badge', array_merge([
            'text' => $text,
            'variant' => $variant
        ], $options));
    }
}

if (!function_exists('status_badge')) {
    /**
     * Render a status badge component
     * 
     * @param int $status Status code
     * @param array $options Additional options
     * @return string Rendered status badge HTML
     */
    function status_badge(int $status, array $options = []): string
    {
        return component('status_badge', array_merge([
            'status' => $status
        ], $options));
    }
}

if (!function_exists('form_group')) {
    /**
     * Render a form group component
     * 
     * @param string $name Field name
     * @param string $label Field label
     * @param array $options Field options
     * @return string Rendered form group HTML
     */
    function form_group(string $name, string $label = '', array $options = []): string
    {
        return component('form_group', array_merge([
            'name' => $name,
            'label' => $label
        ], $options));
    }
}

if (!function_exists('stats_card')) {
    /**
     * Render a statistics card component
     * 
     * @param string $title Card title
     * @param mixed $value Statistics value
     * @param array $options Additional options
     * @return string Rendered stats card HTML
     */
    function stats_card(string $title, $value, array $options = []): string
    {
        return component('stats_card', array_merge([
            'title' => $title,
            'value' => $value
        ], $options));
    }
}

if (!function_exists('empty_state')) {
    /**
     * Render an empty state component
     * 
     * @param string $title Empty state title
     * @param string $description Empty state description
     * @param array $options Additional options
     * @return string Rendered empty state HTML
     */
    function empty_state(string $title = '', string $description = '', array $options = []): string
    {
        return component('empty_state', array_merge([
            'title' => $title,
            'description' => $description
        ], $options));
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format currency value
     * 
     * @param float $amount Amount to format
     * @param string $currency Currency code
     * @return string Formatted currency
     */
    function format_currency(float $amount, string $currency = 'IDR'): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date for display
     * 
     * @param string $date Date string
     * @param string $format Date format
     * @return string Formatted date
     */
    function format_date(string $date, string $format = 'M j, Y'): string
    {
        return date($format, strtotime($date));
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format datetime for display
     * 
     * @param string $datetime Datetime string
     * @param string $format Datetime format
     * @return string Formatted datetime
     */
    function format_datetime(string $datetime, string $format = 'M j, Y g:i A'): string
    {
        return date($format, strtotime($datetime));
    }
}

if (!function_exists('time_ago')) {
    /**
     * Convert timestamp to human readable time ago format
     * 
     * @param string $datetime Datetime string
     * @return string Time ago string
     */
    function time_ago(string $datetime): string
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) {
            return 'Just now';
        } elseif ($time < 3600) {
            $minutes = floor($time / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($time < 86400) {
            $hours = floor($time / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($time < 2592000) {
            $days = floor($time / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', strtotime($datetime));
        }
    }
}

if (!function_exists('truncate_text')) {
    /**
     * Truncate text to specified length
     * 
     * @param string $text Text to truncate
     * @param int $length Maximum length
     * @param string $suffix Suffix to append
     * @return string Truncated text
     */
    function truncate_text(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $suffix;
    }
}

if (!function_exists('get_user_role_name')) {
    /**
     * Get user role name from level
     * 
     * @param int $level User level
     * @return string Role name
     */
    function get_user_role_name(int $level): string
    {
        return match($level) {
            USER_LEVEL_ADMIN => 'Administrator',
            USER_LEVEL_COURIER => 'Kurir',
            USER_LEVEL_GUDANG => 'Warehouse',
            default => 'Unknown'
        };
    }
}

if (!function_exists('can_access')) {
    /**
     * Check if current user can access a resource
     * 
     * @param array|int $allowedLevels Allowed user levels
     * @return bool Whether user can access
     */
    function can_access($allowedLevels): bool
    {
        $userLevel = session('level');
        
        if (is_array($allowedLevels)) {
            return in_array($userLevel, $allowedLevels);
        }
        
        return $userLevel === $allowedLevels;
    }
}

if (!function_exists('generate_csrf_field')) {
    /**
     * Generate CSRF field for forms
     * 
     * @return string CSRF field HTML
     */
    function generate_csrf_field(): string
    {
        return csrf_field();
    }
}

if (!function_exists('asset_url')) {
    /**
     * Generate asset URL
     * 
     * @param string $path Asset path
     * @return string Full asset URL
     */
    function asset_url(string $path): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}if (
!function_exists('modal')) {
    /**
     * Render a modal component
     * 
     * @param string $id Modal ID
     * @param string $title Modal title
     * @param string $content Modal content
     * @param array $options Additional options
     * @return string Rendered modal HTML
     */
    function modal(string $id, string $title = '', string $content = '', array $options = []): string
    {
        return component('modal', array_merge([
            'id' => $id,
            'title' => $title,
            'content' => $content
        ], $options));
    }
}

if (!function_exists('toast')) {
    /**
     * Render a toast notification component
     * 
     * @param string $message Toast message
     * @param string $type Toast type
     * @param array $options Additional options
     * @return string Rendered toast HTML
     */
    function toast(string $message, string $type = 'info', array $options = []): string
    {
        return component('toast', array_merge([
            'message' => $message,
            'type' => $type
        ], $options));
    }
}

if (!function_exists('skeleton')) {
    /**
     * Render a skeleton loading component
     * 
     * @param string $type Skeleton type
     * @param array $options Additional options
     * @return string Rendered skeleton HTML
     */
    function skeleton(string $type = 'text', array $options = []): string
    {
        return component('skeleton', array_merge([
            'type' => $type
        ], $options));
    }
}

if (!function_exists('chart')) {
    /**
     * Render a chart component
     * 
     * @param string $id Chart ID
     * @param string $type Chart type
     * @param array $data Chart data
     * @param array $options Additional options
     * @return string Rendered chart HTML
     */
    function chart(string $id, string $type, array $data, array $options = []): string
    {
        return component('chart', array_merge([
            'id' => $id,
            'type' => $type,
            'data' => $data
        ], $options));
    }
}

if (!function_exists('analytics_card')) {
    /**
     * Render an analytics card component
     * 
     * @param string $title Card title
     * @param mixed $value Card value
     * @param array $options Additional options
     * @return string Rendered analytics card HTML
     */
    function analytics_card(string $title, $value, array $options = []): string
    {
        return component('analytics_card', array_merge([
            'title' => $title,
            'value' => $value
        ], $options));
    }
}

if (!function_exists('progress_bar')) {
    /**
     * Generate progress bar HTML
     * 
     * @param int $value Progress value
     * @param int $max Maximum value
     * @param array $options Additional options
     * @return string Progress bar HTML
     */
    function progress_bar(int $value, int $max = 100, array $options = []): string
    {
        $percentage = $max > 0 ? round(($value / $max) * 100) : 0;
        $color = $options['color'] ?? 'primary';
        $animated = $options['animated'] ?? false;
        $striped = $options['striped'] ?? false;
        $showLabel = $options['showLabel'] ?? true;
        $height = $options['height'] ?? '1rem';
        
        $classes = ['progress-bar', "bg-{$color}"];
        if ($animated) $classes[] = 'progress-bar-animated';
        if ($striped) $classes[] = 'progress-bar-striped';
        
        return sprintf(
            '<div class="progress" style="height: %s">
                <div class="%s" role="progressbar" style="width: %d%%" aria-valuenow="%d" aria-valuemin="0" aria-valuemax="%d">
                    %s
                </div>
            </div>',
            $height,
            implode(' ', $classes),
            $percentage,
            $value,
            $max,
            $showLabel ? "{$percentage}%" : ''
        );
    }
}

if (!function_exists('notification_badge')) {
    /**
     * Generate notification badge HTML
     * 
     * @param int $count Notification count
     * @param array $options Additional options
     * @return string Badge HTML
     */
    function notification_badge(int $count, array $options = []): string
    {
        if ($count <= 0) return '';
        
        $pulse = $options['pulse'] ?? false;
        $max = $options['max'] ?? 99;
        
        $displayCount = $count > $max ? "{$max}+" : $count;
        $classes = ['notification-badge'];
        if ($pulse) $classes[] = 'pulse';
        
        return sprintf(
            '<span class="%s">%s</span>',
            implode(' ', $classes),
            $displayCount
        );
    }
}

if (!function_exists('loading_spinner')) {
    /**
     * Generate loading spinner HTML
     * 
     * @param string $size Spinner size (sm, lg)
     * @param string $color Spinner color
     * @return string Spinner HTML
     */
    function loading_spinner(string $size = '', string $color = 'primary'): string
    {
        $classes = ['spinner-border'];
        if ($size) $classes[] = "spinner-border-{$size}";
        if ($color !== 'primary') $classes[] = "text-{$color}";
        
        return sprintf(
            '<div class="%s" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>',
            implode(' ', $classes)
        );
    }
}

if (!function_exists('breadcrumb')) {
    /**
     * Generate breadcrumb navigation
     * 
     * @param array $items Breadcrumb items
     * @param array $options Additional options
     * @return string Breadcrumb HTML
     */
    function breadcrumb(array $items, array $options = []): string
    {
        if (empty($items)) return '';
        
        $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
        
        foreach ($items as $index => $item) {
            $isLast = $index === count($items) - 1;
            $classes = ['breadcrumb-item'];
            if ($isLast) $classes[] = 'active';
            
            $html .= sprintf('<li class="%s"', implode(' ', $classes));
            if ($isLast) $html .= ' aria-current="page"';
            $html .= '>';
            
            if (!$isLast && isset($item['url'])) {
                $html .= sprintf('<a href="%s">%s</a>', $item['url'], esc($item['title']));
            } else {
                $html .= esc($item['title']);
            }
            
            $html .= '</li>';
        }
        
        $html .= '</ol></nav>';
        return $html;
    }
}

if (!function_exists('alert')) {
    /**
     * Generate alert component
     * 
     * @param string $message Alert message
     * @param string $type Alert type
     * @param array $options Additional options
     * @return string Alert HTML
     */
    function alert(string $message, string $type = 'info', array $options = []): string
    {
        $dismissible = $options['dismissible'] ?? true;
        $icon = $options['icon'] ?? true;
        
        $classes = ['alert', "alert-{$type}"];
        if ($dismissible) $classes[] = 'alert-dismissible fade show';
        
        $iconClass = match($type) {
            'success' => 'fas fa-check-circle',
            'danger' => 'fas fa-exclamation-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'info' => 'fas fa-info-circle',
            default => 'fas fa-info-circle'
        };
        
        $html = sprintf('<div class="%s" role="alert">', implode(' ', $classes));
        
        if ($icon) {
            $html .= sprintf('<i class="%s me-2"></i>', $iconClass);
        }
        
        $html .= esc($message);
        
        if ($dismissible) {
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        }
        
        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('tooltip')) {
    /**
     * Add tooltip attributes to an element
     * 
     * @param string $text Tooltip text
     * @param string $placement Tooltip placement
     * @return string Tooltip attributes
     */
    function tooltip(string $text, string $placement = 'top'): string
    {
        return sprintf(
            'data-bs-toggle="tooltip" data-bs-placement="%s" title="%s"',
            $placement,
            esc($text)
        );
    }
}

if (!function_exists('popover')) {
    /**
     * Add popover attributes to an element
     * 
     * @param string $title Popover title
     * @param string $content Popover content
     * @param string $placement Popover placement
     * @return string Popover attributes
     */
    function popover(string $title, string $content, string $placement = 'top'): string
    {
        return sprintf(
            'data-bs-toggle="popover" data-bs-placement="%s" data-bs-title="%s" data-bs-content="%s"',
            $placement,
            esc($title),
            esc($content)
        );
    }
}