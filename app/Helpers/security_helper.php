<?php

if (!function_exists('xss_clean')) {
    /**
     * Clean input data for XSS
     */
    function xss_clean($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = xss_clean($value);
            }
            return $data;
        }

        if (is_string($data)) {
            // Remove null bytes
            $data = str_replace(chr(0), '', $data);
            
            // Remove carriage returns
            $data = str_replace(["\r\n", "\r", "\n"], '', $data);
            
            // Remove dangerous HTML tags and attributes
            $data = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $data);
            $data = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi', '', $data);
            $data = preg_replace('/javascript:/i', '', $data);
            $data = preg_replace('/vbscript:/i', '', $data);
            $data = preg_replace('/onload/i', '', $data);
            $data = preg_replace('/onerror/i', '', $data);
            $data = preg_replace('/onclick/i', '', $data);
            $data = preg_replace('/onmouseover/i', '', $data);
            
            return $data;
        }

        return $data;
    }
}

if (!function_exists('safe_output')) {
    /**
     * Safely output data with HTML escaping
     */
    function safe_output($data, $encoding = 'UTF-8')
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = safe_output($value, $encoding);
            }
            return $data;
        }

        return htmlspecialchars((string) $data, ENT_QUOTES | ENT_HTML5, $encoding);
    }
}

if (!function_exists('sanitize_filename')) {
    /**
     * Sanitize filename for safe file operations
     */
    function sanitize_filename(string $filename): string
    {
        // Remove directory traversal attempts
        $filename = basename($filename);
        
        // Remove dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Remove multiple dots (except for extension)
        $parts = explode('.', $filename);
        if (count($parts) > 2) {
            $extension = array_pop($parts);
            $name = implode('_', $parts);
            $filename = $name . '.' . $extension;
        }
        
        return $filename;
    }
}

if (!function_exists('validate_csrf')) {
    /**
     * Validate CSRF token manually
     */
    function validate_csrf(): bool
    {
        $request = \Config\Services::request();
        $security = \Config\Services::security();
        
        return $security->verify($request);
    }
}

if (!function_exists('generate_csrf_hash')) {
    /**
     * Generate CSRF hash for forms
     */
    function generate_csrf_hash(): string
    {
        $security = \Config\Services::security();
        return $security->getHash();
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get CSRF token name
     */
    function csrf_token(): string
    {
        $config = config('Security');
        return $config->tokenName;
    }
}

if (!function_exists('csrf_hash')) {
    /**
     * Get CSRF hash value
     */
    function csrf_hash(): string
    {
        return generate_csrf_hash();
    }
}

if (!function_exists('secure_random_string')) {
    /**
     * Generate cryptographically secure random string
     */
    function secure_random_string(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}

if (!function_exists('hash_password')) {
    /**
     * Hash password securely
     */
    function hash_password(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 3,         // 3 threads
        ]);
    }
}

if (!function_exists('verify_password')) {
    /**
     * Verify password against hash
     */
    function verify_password(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}