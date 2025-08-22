<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class KategoriEntity extends Entity
{
    protected $attributes = [
        'id_kategori' => null,
        'nama' => null,
        'keterangan' => null,
    ];

    protected $casts = [
        'id_kategori' => 'string',
        'nama' => 'string',
        'keterangan' => '?string',
    ];

    protected $datamap = [];

    /**
     * Get the display name for the category
     */
    public function getDisplayName(): string
    {
        return $this->attributes['nama'] ?? '';
    }

    /**
     * Get the description or return default text
     */
    public function getDescription(): string
    {
        return $this->attributes['keterangan'] ?? 'No description available';
    }

    /**
     * Check if category has description
     */
    public function hasDescription(): bool
    {
        return !empty($this->attributes['keterangan']);
    }

    /**
     * Generate next category ID
     */
    public static function generateNextId(string $lastId = ''): string
    {
        $prefix = 'KTG';
        $code = '01';
        
        if (!empty($lastId)) {
            $number = (int) substr($lastId, 3, 2) + 1;
            $code = str_pad($number, 2, '0', STR_PAD_LEFT);
        }
        
        return $prefix . $code;
    }
}