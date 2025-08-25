<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class DetailPengirimanEntity extends Entity
{
    protected $attributes = [
        'id_detail' => null,
        'id_pengiriman' => null,
        'id_barang' => null,
        'qty' => null,
        // Virtual fields from joins
        'barang_nama' => null,
        'barang_nama' => null,
        'barang_satuan' => null,
        'barang_del_no' => null,
        'kategori_nama' => null,
    ];

    protected $casts = [
        'id_detail' => 'integer',
        'id_pengiriman' => 'string',
        'id_barang' => 'string',
        'qty' => 'integer',
        'harga' => 'float',
        'barang_nama' => null,
        'barang_nama' => '?string',
        'barang_satuan' => '?string',
        'barang_del_no' => '?string',
        'kategori_nama' => '?string',
    ];

    protected $datamap = [];

    /**
     * Get item name
     */
    public function getItemName(): string
    {
        return $this->attributes['barang_nama'] ?? '';
    }

    /**
     * Get item unit
     */
    public function getItemUnit(): string
    {
        return $this->attributes['barang_satuan'] ?? '';
    }

    /**
     * Get delivery number
     */
    public function getDeliveryNumber(): string
    {
        return $this->attributes['barang_del_no'] ?? '';
    }

    /**
     * Get category name
     */
    public function getCategoryName(): string
    {
        return $this->attributes['kategori_nama'] ?? '';
    }

    /**
     * Get quantity
     */
    public function getQuantity(): int
    {
        return (int) ($this->attributes['qty'] ?? 0);
    }

    /**
     * Get formatted quantity with unit
     */
    public function getFormattedQuantity(): string
    {
        return $this->getQuantity() . ' ' . $this->getItemUnit();
    }

    /**
     * Get full item description
     */
    public function getFullDescription(): string
    {
        $parts = [];
        
        if (!empty($this->attributes['barang_nama'])) {
            $parts[] = $this->attributes['barang_nama'];
        }
        
        if (!empty($this->attributes['qty'])) {
            $parts[] = '(' . $this->getFormattedQuantity() . ')';
        }
        
        return implode(' ', $parts);
    }

    /**
     * Check if quantity is valid
     */
    public function hasValidQuantity(): bool
    {
        return $this->getQuantity() > 0;
    }

    /**
     * Get item summary for display
     */
    public function getItemSummary(): array
    {
        return [
            'id_barang' => $this->attributes['id_barang'],
            'nama' => $this->getItemName(),
            'qty' => $this->getQuantity(),
            'satuan' => $this->getItemUnit(),
            'del_no' => $this->getDeliveryNumber(),
            'kategori' => $this->getCategoryName(),
            'formatted_qty' => $this->getFormattedQuantity(),
            'description' => $this->getFullDescription()
        ];
    }
}