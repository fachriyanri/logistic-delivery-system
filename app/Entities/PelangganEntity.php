<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class PelangganEntity extends Entity
{
    protected $attributes = [
        'id_pelanggan' => null,
        'nama' => null,
        'telepon' => null,
        'alamat' => null,
    ];

    protected $casts = [
        'id_pelanggan' => 'string',
        'nama' => 'string',
        'telepon' => 'string',
        'alamat' => 'string',
    ];

    protected $datamap = [];

    /**
     * Get the display name for the customer
     */
    public function getDisplayName(): string
    {
        return $this->attributes['nama'] ?? '';
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhone(): string
    {
        $phone = $this->attributes['telepon'] ?? '';
        
        // Format Indonesian phone number
        if (strlen($phone) > 10) {
            return substr($phone, 0, 4) . '-' . substr($phone, 4, 4) . '-' . substr($phone, 8);
        }
        
        return $phone;
    }

    /**
     * Get address
     */
    public function getAddress(): string
    {
        return $this->attributes['alamat'] ?? '';
    }

    /**
     * Get short address (truncated)
     */
    public function getShortAddress(int $length = 50): string
    {
        $address = $this->getAddress();
        
        if (strlen($address) > $length) {
            return substr($address, 0, $length) . '...';
        }
        
        return $address;
    }

    /**
     * Check if customer has address
     */
    public function hasAddress(): bool
    {
        return !empty($this->attributes['alamat']);
    }

    /**
     * Generate next customer ID
     */
    public static function generateNextId(string $lastId = ''): string
    {
        $prefix = 'CST';
        $code = '0001';
        
        if (!empty($lastId)) {
            $number = (int) substr($lastId, 3, 4) + 1;
            $code = str_pad($number, 4, '0', STR_PAD_LEFT);
        }
        
        return $prefix . $code;
    }

    /**
     * Get formatted customer code for display
     */
    public function getFormattedCode(): string
    {
        return $this->attributes['id_pelanggan'] ?? '';
    }

    /**
     * Get full customer information
     */
    public function getFullInfo(): string
    {
        $parts = [];
        
        if (!empty($this->attributes['nama'])) {
            $parts[] = $this->attributes['nama'];
        }
        
        if (!empty($this->attributes['telepon'])) {
            $parts[] = '(' . $this->getFormattedPhone() . ')';
        }
        
        return implode(' ', $parts);
    }

    /**
     * Get customer contact information
     */
    public function getContactInfo(): array
    {
        return [
            'nama' => $this->getDisplayName(),
            'telepon' => $this->getFormattedPhone(),
            'alamat' => $this->getAddress()
        ];
    }

    /**
     * Check if customer can be deleted (not used in shipments)
     */
    public function canBeDeleted(): bool
    {
        // This will be checked in the model/service layer
        return true;
    }

    /**
     * Get customer summary for display
     */
    public function getSummary(): string
    {
        $summary = $this->getDisplayName();
        
        if (!empty($this->attributes['telepon'])) {
            $summary .= ' - ' . $this->getFormattedPhone();
        }
        
        return $summary;
    }

    /**
     * Validate phone number format
     */
    public function isValidPhone(): bool
    {
        $phone = $this->attributes['telepon'] ?? '';
        
        // Basic validation for Indonesian phone numbers
        return preg_match('/^[0-9+\-\s()]+$/', $phone) && strlen($phone) >= 10;
    }

    /**
     * Get customer type based on name (simple heuristic)
     */
    public function getCustomerType(): string
    {
        $name = strtolower($this->getDisplayName());
        
        // Check for common company indicators
        $companyIndicators = ['pt', 'cv', 'ud', 'tbk', 'corp', 'ltd', 'inc', 'group'];
        
        foreach ($companyIndicators as $indicator) {
            if (strpos($name, $indicator) !== false) {
                return 'Corporate';
            }
        }
        
        return 'Individual';
    }
}