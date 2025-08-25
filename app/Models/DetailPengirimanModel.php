<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\DetailPengirimanEntity;

class DetailPengirimanModel extends Model
{
    protected $table = 'detail_pengiriman';
    protected $primaryKey = 'id_detail';
    protected $returnType = DetailPengirimanEntity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_pengiriman',
        'id_barang',
        'qty'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_pengiriman' => 'required|max_length[14]',
        'id_barang' => 'required|max_length[7]|is_not_unique[barang.id_barang]',
        'qty' => 'required|integer|greater_than[0]'
    ];

    protected $validationMessages = [
        'id_pengiriman' => [
            'required' => 'ID Pengiriman harus diisi',
            'max_length' => 'ID Pengiriman maksimal 14 karakter'
        ],
        'id_barang' => [
            'required' => 'ID Barang harus diisi',
            'max_length' => 'ID Barang maksimal 7 karakter',
            'is_not_unique' => 'Barang tidak valid'
        ],
        'qty' => [
            'required' => 'Jumlah harus diisi',
            'integer' => 'Jumlah harus berupa angka',
            'greater_than' => 'Jumlah harus lebih dari 0'
        ]
    ];

    /**
     * Get details by shipment ID with item information
     */
    public function getByShipmentId(string $shipmentId): array
    {
        $builder = $this->db->table($this->table . ' dp');
        $builder->select("dp.*, b.nama as nama_barang, b.satuan, b.harga, k.nama as nama_kategori, dp.qty as jumlah, '' as keterangan");
        $builder->join('barang b', 'b.id_barang = dp.id_barang', 'left');
        $builder->join('kategori k', 'k.id_kategori = b.id_kategori', 'left');
        $builder->where('dp.id_pengiriman', $shipmentId);
        $builder->orderBy('b.nama', 'ASC');
        
        $results = $builder->get()->getResultArray();
        
        // Convert to entities
        $entities = [];
        foreach ($results as $result) {
            $entity = new DetailPengirimanEntity($result);
            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     * Save multiple details for a shipment
     */
    public function saveShipmentDetails(string $shipmentId, array $details): bool
    {
        // Start transaction
        $this->db->transStart();

        try {
            // Delete existing details
            $this->where('id_pengiriman', $shipmentId)->delete();

            // Insert new details
            foreach ($details as $detail) {
                $data = [
                    'id_pengiriman' => $shipmentId,
                    'id_barang' => $detail['id_barang'],
                    'qty' => (int) $detail['qty']
                ];

                if (!$this->insert($data)) {
                    throw new \Exception('Failed to insert detail');
                }
            }

            $this->db->transComplete();
            return $this->db->transStatus();

        } catch (\Exception $e) {
            $this->db->transRollback();
            return false;
        }
    }

    /**
     * Delete details by shipment ID
     */
    public function deleteByShipmentId(string $shipmentId): bool
    {
        return $this->where('id_pengiriman', $shipmentId)->delete();
    }

    /**
     * Get total quantity for a shipment
     */
    public function getTotalQuantity(string $shipmentId): int
    {
        $result = $this->selectSum('qty')
                      ->where('id_pengiriman', $shipmentId)
                      ->get()
                      ->getRow();
        
        return (int) ($result->qty ?? 0);
    }

    /**
     * Get unique items count for a shipment
     */
    public function getUniqueItemsCount(string $shipmentId): int
    {
        return $this->where('id_pengiriman', $shipmentId)->countAllResults();
    }

    /**
     * Check if item is used in any shipment
     */
    public function isItemUsed(string $itemId): bool
    {
        return $this->where('id_barang', $itemId)->countAllResults() > 0;
    }

    /**
     * Get shipment details summary
     */
    public function getShipmentSummary(string $shipmentId): array
    {
        $details = $this->getByShipmentId($shipmentId);
        
        $summary = [
            'total_items' => count($details),
            'total_quantity' => 0,
            'items' => []
        ];

        foreach ($details as $detail) {
            $summary['total_quantity'] += $detail->getQuantity();
            $summary['items'][] = $detail->getItemSummary();
        }

        return $summary;
    }

    /**
     * Validate detail data
     */
    public function validateDetailData(array $data): array
    {
        $errors = [];

        if (empty($data['id_barang'])) {
            $errors[] = 'ID Barang harus diisi';
        }

        if (empty($data['qty']) || !is_numeric($data['qty']) || (int) $data['qty'] <= 0) {
            $errors[] = 'Jumlah harus berupa angka positif';
        }

        return $errors;
    }

    /**
     * Get details formatted for export
     */
    public function getDetailsForExport(string $shipmentId): array
    {
        $details = $this->getByShipmentId($shipmentId);
        $exportData = [];

        foreach ($details as $detail) {
            $exportData[] = [
                'id_barang' => $detail->id_barang,
                'nama_barang' => $detail->getItemName(),
                'kategori' => $detail->getCategoryName(),
                'qty' => $detail->getQuantity(),
                'satuan' => $detail->getItemUnit(),
                'del_no' => $detail->getDeliveryNumber()
            ];
        }

        return $exportData;
    }
}