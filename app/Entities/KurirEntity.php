<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class KurirEntity extends Entity
{
    protected $attributes = [
        'id_kurir' => null,
        'nama' => null,
        'jenis_kelamin' => null,
        'telepon' => null,
        'alamat' => null,
        'password' => null,
    ];

    protected $casts = [
        'id_kurir' => 'string',
        'nama' => 'string',
        'jenis_kelamin' => 'string',
        'telepon' => 'string',
        'alamat' => '?string',
        'password' => 'string',
    ];

    protected $datamap = [];

    /**
     * Get the display name for the courier
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
     * Get gender display text
     */
    public function getGenderText(): string
    {
        return $this->attributes['jenis_kelamin'] ?? '';
    }

    /**
     * Get address or default text
     */
    public function getAddress(): string
    {
        return $this->attributes['alamat'] ?? 'Alamat tidak tersedia';
    }

    /**
     * Check if courier has address
     */
    public function hasAddress(): bool
    {
        return !empty($this->attributes['alamat']);
    }

    /**
     * Set password with hashing
     */
    public function setPassword(string $password): self
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_ARGON2ID);
        return $this;
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->attributes['password'] ?? '');
    }

    /**
     * Check if courier is male
     */
    public function isMale(): bool
    {
        return strtolower($this->attributes['jenis_kelamin'] ?? '') === 'laki-laki';
    }

    /**
     * Check if courier is female
     */
    public function isFemale(): bool
    {
        return strtolower($this->attributes['jenis_kelamin'] ?? '') === 'perempuan';
    }

    /**
     * Generate next courier ID
     */
    public static function generateNextId(string $lastId = ''): string
    {
        $prefix = 'KRR';
        $code = '01';
        
        if (!empty($lastId)) {
            $number = (int) substr($lastId, 3, 2) + 1;
            $code = str_pad($number, 2, '0', STR_PAD_LEFT);
        }
        
        return $prefix . $code;
    }

    /**
     * Get formatted courier code for display
     */
    public function getFormattedCode(): string
    {
        return $this->attributes['id_kurir'] ?? '';
    }

    /**
     * Get full courier information
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
     * Check if courier can be deleted (not used in shipments)
     */
    public function canBeDeleted(): bool
    {
        // This will be checked in the model/service layer
        return true;
    }

    /**
     * Get gender options for forms
     */
    public static function getGenderOptions(): array
    {
        return [
            'Laki-Laki' => 'Laki-Laki',
            'Perempuan' => 'Perempuan'
        ];
    }
}