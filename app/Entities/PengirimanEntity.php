<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class PengirimanEntity extends Entity
{
    protected $attributes = [
        'id_pengiriman' => null,
        'tanggal' => null,
        'id_pelanggan' => null,
        'id_kurir' => null,
        'no_kendaraan' => null,
        'no_po' => null,
        'detail_location' => null,
        'keterangan' => null,
        'penerima' => null,
        'photo' => null,
        'status' => null,
        'created_at' => null,
        'updated_at' => null,
        // Virtual fields from joins
        'nama_pelanggan' => null,
        'alamat_pelanggan' => null,
        'telepon_pelanggan' => null,
        'nama_kurir' => null,
        'alamat_kurir' => null,
        'telepon_kurir' => null,
        'details' => null,
    ];

    protected $casts = [
        'id_pengiriman' => 'string',
        'tanggal' => 'datetime',
        'id_pelanggan' => 'string',
        'id_kurir' => 'string',
        'no_kendaraan' => 'string',
        'no_po' => 'string',
        'detail_location' => '?string',
        'keterangan' => '?string',
        'penerima' => '?string',
        'photo' => '?string',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'pelanggan_nama' => '?string',
        'pelanggan_alamat' => '?string',
        'kurir_nama' => '?string',
    ];

    protected $datamap = [];

    // Status constants - aligned with form values
    const STATUS_PENDING = 1;       // Pending
    const STATUS_IN_TRANSIT = 2;    // Dalam Perjalanan
    const STATUS_DELIVERED = 3;     // Terkirim
    const STATUS_CANCELLED = 4;     // Dibatalkan
    
    // Legacy constants for backward compatibility
    const STATUS_SENT = 1;          // @deprecated Use STATUS_PENDING
    const STATUS_RECEIVED = 3;      // @deprecated Use STATUS_DELIVERED
    const STATUS_REJECTED = 4;      // @deprecated Use STATUS_CANCELLED
    const STATUS_PARTIAL = 2;       // @deprecated Use STATUS_IN_TRANSIT

    /**
     * Get status text
     */
    public function getStatusText(): string
    {
        $statuses = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_TRANSIT => 'Dalam Perjalanan',
            self::STATUS_DELIVERED => 'Terkirim',
            self::STATUS_CANCELLED => 'Dibatalkan'
        ];

        return $statuses[$this->attributes['status']] ?? 'Unknown';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        $classes = [
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_IN_TRANSIT => 'bg-info',
            self::STATUS_DELIVERED => 'bg-success',
            self::STATUS_CANCELLED => 'bg-danger'
        ];

        return $classes[$this->attributes['status']] ?? 'bg-secondary';
    }

    /**
     * Check if shipment is delivered/received
     */
    public function isDelivered(): bool
    {
        return $this->attributes['status'] === self::STATUS_DELIVERED;
    }

    /**
     * Check if shipment is pending/sent
     */
    public function isPending(): bool
    {
        return $this->attributes['status'] === self::STATUS_PENDING;
    }

    /**
     * Check if shipment is in transit
     */
    public function isInTransit(): bool
    {
        return $this->attributes['status'] === self::STATUS_IN_TRANSIT;
    }

    /**
     * Check if shipment is cancelled/rejected
     */
    public function isCancelled(): bool
    {
        return $this->attributes['status'] === self::STATUS_CANCELLED;
    }

    /**
     * Check if shipment is rejected (legacy)
     */
    public function isRejected(): bool
    {
        return $this->isCancelled();
    }

    /**
     * Check if shipment can be modified
     */
    public function canBeModified(): bool
    {
        return $this->isPending();
    }

    /**
     * Get formatted date
     */
    public function getFormattedDate(): string
    {
        if ($this->attributes['tanggal']) {
            return date('d/m/Y', strtotime($this->attributes['tanggal']));
        }
        return '';
    }

    /**
     * Get customer name
     */
    public function getCustomerName(): string
    {
        return $this->attributes['pelanggan_nama'] ?? '';
    }

    /**
     * Get customer address
     */
    public function getCustomerAddress(): string
    {
        return $this->attributes['pelanggan_alamat'] ?? '';
    }

    /**
     * Get courier name
     */
    public function getCourierName(): string
    {
        return $this->attributes['kurir_nama'] ?? '';
    }

    /**
     * Get QR code data
     */
    public function getQRCodeData(): string
    {
        return json_encode([
            'id_pengiriman' => $this->attributes['id_pengiriman'],
            'tanggal' => $this->attributes['tanggal'],
            'pelanggan' => $this->getCustomerName(),
            'kurir' => $this->getCourierName(),
            'no_po' => $this->attributes['no_po'],
            'status' => $this->getStatusText()
        ]);
    }

    /**
     * Check if photo exists
     */
    public function hasPhoto(): bool
    {
        return !empty($this->attributes['photo']);
    }

    /**
     * Get photo URL
     */
    public function getPhotoUrl(): ?string
    {
        if ($this->hasPhoto()) {
            return base_url('uploads/photos/' . $this->attributes['photo']);
        }
        return null;
    }

    /**
     * Generate next shipment ID
     */
    public static function generateNextId(string $lastId = ''): string
    {
        $prefix = 'KRM' . date('Ymd');
        $code = '001';
        
        if (!empty($lastId) && strpos($lastId, $prefix) === 0) {
            $number = (int) substr($lastId, 10, 3) + 1;
            $code = str_pad($number, 3, '0', STR_PAD_LEFT);
        }
        
        return $prefix . $code;
    }

    /**
     * Get shipment summary
     */
    public function getSummary(): array
    {
        return [
            'id_pengiriman' => $this->attributes['id_pengiriman'],
            'tanggal' => $this->getFormattedDate(),
            'pelanggan' => $this->getCustomerName(),
            'kurir' => $this->getCourierName(),
            'no_po' => $this->attributes['no_po'],
            'no_kendaraan' => $this->attributes['no_kendaraan'],
            'status' => $this->getStatusText(),
            'status_class' => $this->getStatusBadgeClass()
        ];
    }

    /**
     * Check if shipment has receiver information
     */
    public function hasReceiverInfo(): bool
    {
        return !empty($this->attributes['penerima']);
    }

    /**
     * Get receiver name
     */
    public function getReceiverName(): string
    {
        return $this->attributes['penerima'] ?? '';
    }

    /**
     * Get notes/description
     */
    public function getNotes(): string
    {
        return $this->attributes['keterangan'] ?? '';
    }

    /**
     * Check if shipment has notes
     */
    public function hasNotes(): bool
    {
        return !empty($this->attributes['keterangan']);
    }

    /**
     * Get vehicle number
     */
    public function getVehicleNumber(): string
    {
        return $this->attributes['no_kendaraan'] ?? '';
    }

    /**
     * Get PO number
     */
    public function getPONumber(): string
    {
        return $this->attributes['no_po'] ?? '';
    }

    /**
     * Get detail location
     */
    public function getDetailLocation(): string
    {
        return $this->attributes['detail_location'] ?? '';
    }

    /**
     * Check if detail location exists
     */
    public function hasDetailLocation(): bool
    {
        return !empty($this->attributes['detail_location']);
    }

    /**
     * Get status options for forms
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_TRANSIT => 'Dalam Perjalanan',
            self::STATUS_DELIVERED => 'Terkirim',
            self::STATUS_CANCELLED => 'Dibatalkan'
        ];
    }

    /**
     * Check if status requires receiver information
     * Note: Penerima field has been removed from forms, so this returns false
     */
    public function statusRequiresReceiver(): bool
    {
        // Penerima field is no longer used in the system
        return false;
    }
}