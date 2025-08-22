<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\PengirimanEntity;

class PengirimanModel extends Model
{
    protected $table = 'pengiriman';
    protected $primaryKey = 'id_pengiriman';
    protected $returnType = PengirimanEntity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_pengiriman',
        'tanggal',
        'id_pelanggan',
        'id_kurir',
        'no_kendaraan',
        'no_po',
        'keterangan',
        'penerima',
        'photo',
        'status'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_pengiriman' => 'required|max_length[14]|is_unique[pengiriman.id_pengiriman,id_pengiriman,{id_pengiriman}]',
        'tanggal' => 'required|valid_date',
        'id_pelanggan' => 'required|is_not_unique[pelanggan.id_pelanggan]',
        'id_kurir' => 'required|is_not_unique[kurir.id_kurir]',
        'no_kendaraan' => 'required|max_length[8]',
        'no_po' => 'required|max_length[15]',
        'status' => 'required|in_list[1,2,3,4]'
    ];

    protected $validationMessages = [
        'id_pengiriman' => [
            'required' => 'ID Pengiriman harus diisi',
            'max_length' => 'ID Pengiriman maksimal 14 karakter',
            'is_unique' => 'ID Pengiriman sudah terdaftar'
        ],
        'tanggal' => [
            'required' => 'Tanggal harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'id_pelanggan' => [
            'required' => 'Pelanggan harus dipilih',
            'is_not_unique' => 'Pelanggan tidak valid'
        ],
        'id_kurir' => [
            'required' => 'Kurir harus dipilih',
            'is_not_unique' => 'Kurir tidak valid'
        ],
        'no_kendaraan' => [
            'required' => 'Nomor kendaraan harus diisi',
            'max_length' => 'Nomor kendaraan maksimal 8 karakter'
        ],
        'no_po' => [
            'required' => 'Nomor PO harus diisi',
            'max_length' => 'Nomor PO maksimal 15 karakter'
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status tidak valid'
        ]
    ];

    /**
     * Get all shipments with filtering and pagination
     */
    public function getAllWithFilter(array $filter = [], int $limit = 15, int $offset = 0, string $orderBy = 'id_pengiriman', string $orderType = 'DESC'): array
    {
        $builder = $this->db->table($this->table . ' pg');
        $builder->select('pg.*, p.nama as pelanggan_nama, p.alamat as pelanggan_alamat, k.nama as kurir_nama');
        $builder->join('pelanggan p', 'p.id_pelanggan = pg.id_pelanggan', 'left');
        $builder->join('kurir k', 'k.id_kurir = pg.id_kurir', 'left');

        // Apply keyword filter
        if (!empty($filter['keyword'])) {
            $keyword = strtolower($filter['keyword']);
            $builder->groupStart()
                    ->like('LOWER(pg.id_pengiriman)', $keyword)
                    ->orLike('LOWER(pg.no_po)', $keyword)
                    ->orLike('LOWER(pg.no_kendaraan)', $keyword)
                    ->orLike('LOWER(p.nama)', $keyword)
                    ->orLike('LOWER(k.nama)', $keyword)
                    ->groupEnd();
        }

        // Apply status filter
        if (!empty($filter['status'])) {
            $builder->where('pg.status', $filter['status']);
        }

        // Apply date range filter
        if (!empty($filter['from']) && !empty($filter['to'])) {
            $builder->where('pg.tanggal >=', $filter['from']);
            $builder->where('pg.tanggal <=', $filter['to']);
        }

        // Apply customer filter
        if (!empty($filter['id_pelanggan'])) {
            $builder->where('pg.id_pelanggan', $filter['id_pelanggan']);
        }

        // Apply courier filter
        if (!empty($filter['id_kurir'])) {
            $builder->where('pg.id_kurir', $filter['id_kurir']);
        }

        // Get total count
        $total = $builder->countAllResults(false);

        // Apply ordering and pagination
        $builder->orderBy($orderBy, $orderType);
        
        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        $results = $builder->get()->getResultArray();
        
        // Convert to entities
        $entities = [];
        foreach ($results as $result) {
            $entity = new PengirimanEntity($result);
            $entities[] = $entity;
        }

        return [$entities, $total];
    }

    /**
     * Get shipment by ID with related data
     */
    public function getWithDetails(string $id): ?PengirimanEntity
    {
        $builder = $this->db->table($this->table . ' pg');
        $builder->select('pg.*, p.nama as nama_pelanggan, p.alamat as alamat_pelanggan, p.telepon as telepon_pelanggan, k.nama as nama_kurir, k.alamat as alamat_kurir, k.telepon as telepon_kurir');
        $builder->join('pelanggan p', 'p.id_pelanggan = pg.id_pelanggan', 'left');
        $builder->join('kurir k', 'k.id_kurir = pg.id_kurir', 'left');
        $builder->where('pg.id_pengiriman', $id);
        
        $result = $builder->get()->getRowArray();
        
        return $result ? new PengirimanEntity($result) : null;
    }

    /**
     * Generate next shipment ID
     */
    public function generateNextId(): string
    {
        $today = date('Ymd');
        $prefix = 'KRM' . $today;
        
        $builder = $this->builder();
        $lastShipment = $builder->like('id_pengiriman', $prefix, 'after')
                              ->orderBy('id_pengiriman', 'DESC')
                              ->get()
                              ->getFirstRow($this->returnType);
        
        if (!$lastShipment) {
            return $prefix . '001';
        }

        $lastNumber = (int) substr($lastShipment->id_pengiriman, -3);
        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Save shipment with validation
     */
    public function savePengiriman(array $data, string $id = ''): bool
    {
        if (empty($id)) {
            // Insert new shipment
            return $this->insert($data) !== false;
        } else {
            // Update existing shipment
            return $this->update($id, $data);
        }
    }

    /**
     * Update shipment status
     */
    public function updateStatus(string $id, int $status, array $additionalData = []): bool
    {
        $updateData = array_merge(['status' => $status], $additionalData);
        return $this->update($id, $updateData);
    }

    /**
     * Get shipments by date range
     */
    public function getShipmentsByDateRange(string $from, string $to, array $filters = []): array
    {
        $filter = array_merge($filters, [
            'from' => $from,
            'to' => $to
        ]);

        [$shipments, $total] = $this->getAllWithFilter($filter, 0, 0, 'pg.tanggal', 'DESC');
        
        return $shipments;
    }

    /**
     * Get shipment statistics
     */
    public function getShipmentStatistics(array $dateRange = []): array
    {
        $builder = $this->builder();
        
        if (!empty($dateRange['from']) && !empty($dateRange['to'])) {
            $builder->where('tanggal >=', $dateRange['from']);
            $builder->where('tanggal <=', $dateRange['to']);
        }

        $totalShipments = $builder->countAllResults(false);
        
        // Get status counts
        $statusCounts = [];
        foreach (PengirimanEntity::getStatusOptions() as $statusId => $statusName) {
            $count = $builder->where('status', $statusId)->countAllResults(false);
            $statusCounts[$statusName] = $count;
        }

        return [
            'total_shipments' => $totalShipments,
            'status_counts' => $statusCounts
        ];
    }

    /**
     * Check if shipment ID exists
     */
    public function isIdExists(string $id, string $excludeId = ''): bool
    {
        $builder = $this->builder();
        $builder->where('id_pengiriman', $id);
        
        if (!empty($excludeId)) {
            $builder->where('id_pengiriman !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get shipments for export
     */
    public function getShipmentsForExport(array $filter = []): array
    {
        [$shipments, $total] = $this->getAllWithFilter($filter, 0, 0, 'pg.tanggal', 'DESC');
        
        $exportData = [];
        foreach ($shipments as $shipment) {
            $exportData[] = [
                'id_pengiriman' => $shipment->id_pengiriman,
                'tanggal' => $shipment->getFormattedDate(),
                'pelanggan' => $shipment->getCustomerName(),
                'no_po' => $shipment->getPONumber(),
                'kurir' => $shipment->getCourierName(),
                'no_kendaraan' => $shipment->getVehicleNumber(),
                'penerima' => $shipment->getReceiverName(),
                'status' => $shipment->getStatusText()
            ];
        }
        
        return $exportData;
    }

    /**
     * Delete shipment with details
     */
    public function deleteShipment(string $id): array
    {
        $result = ['success' => false, 'message' => ''];

        // Check if shipment exists
        $shipment = $this->find($id);
        if (!$shipment) {
            $result['message'] = 'Pengiriman tidak ditemukan';
            return $result;
        }

        // Start transaction
        $this->db->transStart();

        try {
            // Delete shipment details first
            $this->db->table('detail_pengiriman')->where('id_pengiriman', $id)->delete();
            
            // Delete shipment
            $this->delete($id);

            $this->db->transComplete();

            if ($this->db->transStatus()) {
                $result['success'] = true;
                $result['message'] = 'Pengiriman berhasil dihapus';
            } else {
                $result['message'] = 'Gagal menghapus pengiriman';
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }
}