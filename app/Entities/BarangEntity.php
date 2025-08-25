<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class BarangEntity extends Entity
{
    protected $attributes = [
        'id_barang' => null,
        'nama' => null,
        'satuan' => null,
        'id_kategori' => null,
        // Virtual fields from joins
        'kategori_nama' => null,
        'kategori_keterangan' => null,
    ];

    protected $casts = [
        'id_barang' => 'string',
        'nama' => 'string',
        'satuan' => 'string',
        'id_kategori' => 'string',
        'kategori_nama' => '?string',
        'kategori_keterangan' => '?string',
    ];

    protected $datamap = [];

    /**
     * Get the display name for the item
     */
    public function getDisplayName(): string
    {
        return $this->attributes['nama'] ?? '';
    }

    /**
     * Get the category name
     */
    public function getCategoryName(): string
    {
        return $this->attributes['kategori_nama'] ?? 'Tidak ada kategori';
    }

    /**
     * Get the unit of measurement
     */
    public function getUnit(): string
    {
        return $this->attributes['satuan'] ?? '';
    }



    /**
     * Get full item description
     */
    public function getFullDescription(): string
    {
        $parts = [];
        
        if (!empty($this->attributes['nama'])) {
            $parts[] = $this->attributes['nama'];
        }
        
        if (!empty($this->attributes['satuan'])) {
            $parts[] = '(' . $this->attributes['satuan'] . ')';
        }
        
        return implode(' ', $parts);
    }

    /**
     * Check if item has category
     */
    public function hasCategory(): bool
    {
        return !empty($this->attributes['id_kategori']);
    }

    /**
     * Generate next item ID
     */
    public static function generateNextId(string $lastId = ''): string
    {
        $prefix = 'BRG';
        $code = '0001';
        
        if (!empty($lastId)) {
            $number = (int) substr($lastId, 3, 4) + 1;
            $code = str_pad($number, 4, '0', STR_PAD_LEFT);
        }
        
        return $prefix . $code;
    }

    /**
     * Get formatted item code for display
     */
    public function getFormattedCode(): string
    {
        return $this->attributes['id_barang'] ?? '';
    }

    /**
     * Check if item can be deleted (not used in shipments)
     */
    public function canBeDeleted(): bool
    {
        // This will be checked in the model/service layer
        return true;
    }
}