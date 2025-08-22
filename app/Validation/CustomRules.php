<?php

namespace App\Validation;

class CustomRules
{
    /**
     * Validate shipment ID format (KRM + date + sequence)
     */
    public function valid_shipment_id(string $str, ?string $error = null): bool
    {
        // Format: KRM + YYYYMMDD + sequence (e.g., KRM20240101001)
        $pattern = '/^KRM\d{8}\d{3}$/';
        return preg_match($pattern, $str) === 1;
    }

    /**
     * Validate Indonesian phone number
     */
    public function valid_phone_number(string $str, ?string $error = null): bool
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $str);
        
        // Check if it starts with 08, +62, or 62
        if (preg_match('/^(08|628|62)/', $phone)) {
            // Length should be between 10-15 digits
            return strlen($phone) >= 10 && strlen($phone) <= 15;
        }
        
        return false;
    }

    /**
     * Validate vehicle number format
     */
    public function valid_vehicle_number(string $str, ?string $error = null): bool
    {
        // Indonesian vehicle number format: B 1234 ABC or B1234ABC
        $pattern = '/^[A-Z]{1,2}\s?\d{1,4}\s?[A-Z]{1,3}$/i';
        return preg_match($pattern, $str) === 1;
    }

    /**
     * Validate user level
     */
    public function valid_user_level(string $str, ?string $error = null): bool
    {
        $validLevels = [USER_LEVEL_ADMIN, USER_LEVEL_FINANCE, USER_LEVEL_GUDANG];
        return in_array((int) $str, $validLevels);
    }

    /**
     * Validate date format and range
     */
    public function valid_business_date(string $str, ?string $error = null): bool
    {
        $date = \DateTime::createFromFormat('Y-m-d', $str);
        
        if (!$date || $date->format('Y-m-d') !== $str) {
            return false;
        }
        
        // Check if date is not in the future (more than 1 day)
        $today = new \DateTime();
        $today->add(new \DateInterval('P1D')); // Allow tomorrow
        
        return $date <= $today;
    }

    /**
     * Validate currency amount
     */
    public function valid_currency(string $str, ?string $error = null): bool
    {
        // Remove currency symbols and formatting
        $amount = preg_replace('/[^\d.]/', '', $str);
        
        if (!is_numeric($amount)) {
            return false;
        }
        
        $value = (float) $amount;
        return $value >= 0 && $value <= 999999999.99;
    }

    /**
     * Validate weight in kilograms
     */
    public function valid_weight(string $str, ?string $error = null): bool
    {
        if (!is_numeric($str)) {
            return false;
        }
        
        $weight = (float) $str;
        return $weight > 0 && $weight <= 10000; // Max 10 tons
    }

    /**
     * Validate dimensions (length x width x height)
     */
    public function valid_dimensions(string $str, ?string $error = null): bool
    {
        // Format: 100x50x30 or 100 x 50 x 30
        $pattern = '/^\d+(\.\d+)?\s*x\s*\d+(\.\d+)?\s*x\s*\d+(\.\d+)?$/i';
        
        if (!preg_match($pattern, $str)) {
            return false;
        }
        
        // Extract dimensions
        $dimensions = preg_split('/\s*x\s*/i', $str);
        
        foreach ($dimensions as $dimension) {
            $value = (float) $dimension;
            if ($value <= 0 || $value > 1000) { // Max 10 meters per dimension
                return false;
            }
        }
        
        return true;
    }

    /**
     * Validate Indonesian postal code
     */
    public function valid_postal_code(string $str, ?string $error = null): bool
    {
        // Indonesian postal code is 5 digits
        return preg_match('/^\d{5}$/', $str) === 1;
    }

    /**
     * Validate strong password
     */
    public function strong_password(string $str, ?string $error = null): bool
    {
        // At least 8 characters, contains uppercase, lowercase, number
        if (strlen($str) < 8) {
            return false;
        }
        
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $str) === 1;
    }

    /**
     * Validate unique shipment ID excluding current record
     */
    public function unique_shipment_id(string $str, string $field, array $data): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pengiriman');
        
        $builder->where('id_pengiriman', $str);
        
        // If updating, exclude current record
        if (isset($data['id']) && !empty($data['id'])) {
            $builder->where('id_pengiriman !=', $data['id']);
        }
        
        return $builder->countAllResults() === 0;
    }

    /**
     * Validate that customer exists
     */
    public function customer_exists(string $str, ?string $error = null): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pelanggan');
        
        return $builder->where('id_pelanggan', $str)->countAllResults() > 0;
    }

    /**
     * Validate that courier exists
     */
    public function courier_exists(string $str, ?string $error = null): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('kurir');
        
        return $builder->where('id_kurir', $str)->countAllResults() > 0;
    }

    /**
     * Validate that category exists
     */
    public function category_exists(string $str, ?string $error = null): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('kategori');
        
        return $builder->where('id_kategori', $str)->countAllResults() > 0;
    }

    /**
     * Validate file extension
     */
    public function valid_file_extension(string $str, string $extensions): bool
    {
        $allowedExtensions = explode(',', $extensions);
        $fileExtension = strtolower(pathinfo($str, PATHINFO_EXTENSION));
        
        return in_array($fileExtension, array_map('trim', $allowedExtensions));
    }

    /**
     * Validate image file
     */
    public function valid_image(string $str, ?string $error = null): bool
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileExtension = strtolower(pathinfo($str, PATHINFO_EXTENSION));
        
        return in_array($fileExtension, $allowedExtensions);
    }

    /**
     * Validate business hours (for delivery time)
     */
    public function valid_business_hours(string $str, ?string $error = null): bool
    {
        // Format: HH:MM (24-hour format)
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $str)) {
            return false;
        }
        
        $time = \DateTime::createFromFormat('H:i', $str);
        $businessStart = \DateTime::createFromFormat('H:i', '08:00');
        $businessEnd = \DateTime::createFromFormat('H:i', '17:00');
        
        return $time >= $businessStart && $time <= $businessEnd;
    }

    /**
     * Validate quantity (positive integer)
     */
    public function valid_quantity(string $str, ?string $error = null): bool
    {
        if (!ctype_digit($str)) {
            return false;
        }
        
        $quantity = (int) $str;
        return $quantity > 0 && $quantity <= 999999;
    }

    /**
     * Validate status code
     */
    public function valid_status(string $str, string $validStatuses): bool
    {
        $allowedStatuses = explode(',', $validStatuses);
        return in_array($str, array_map('trim', $allowedStatuses));
    }

    /**
     * Validate Indonesian name format
     */
    public function valid_indonesian_name(string $str, ?string $error = null): bool
    {
        // Allow letters, spaces, dots, apostrophes, and common Indonesian characters
        return preg_match('/^[a-zA-Z\s.\'-]+$/u', $str) === 1;
    }

    /**
     * Validate company name format
     */
    public function valid_company_name(string $str, ?string $error = null): bool
    {
        // Allow letters, numbers, spaces, dots, commas, and common business characters
        return preg_match('/^[a-zA-Z0-9\s.,&\'-]+$/u', $str) === 1;
    }

    /**
     * Validate address format
     */
    public function valid_address(string $str, ?string $error = null): bool
    {
        // Allow letters, numbers, spaces, and common address characters
        return preg_match('/^[a-zA-Z0-9\s.,\/\-#]+$/u', $str) === 1;
    }
}