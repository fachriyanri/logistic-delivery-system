<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\PelangganEntity;

class PelangganModel extends Model
{
    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    protected $returnType = PelangganEntity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_pelanggan',
        'nama',
        'telepon',
        'alamat'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_pelanggan' => 'required|max_length[7]|is_unique[pelanggan.id_pelanggan,id_pelanggan,{id_pelanggan}]',
        'nama' => 'required|max_length[30]|is_unique[pelanggan.nama,id_pelanggan,{id_pelanggan}]',
        'telepon' => 'required|max_length[15]',
        'alamat' => 'required|max_length[150]'
    ];

    protected $validationMessages = [
        'id_pelanggan' => [
            'required' => 'ID Pelanggan harus diisi',
            'max_length' => 'ID Pelanggan maksimal 7 karakter',
            'is_unique' => 'ID Pelanggan sudah terdaftar'
        ],
        'nama' => [
            'required' => 'Nama pelanggan harus diisi',
            'max_length' => 'Nama pelanggan maksimal 30 karakter',
            'is_unique' => 'Nama pelanggan sudah terdaftar'
        ],
        'telepon' => [
            'required' => 'Nomor telepon harus diisi',
            'max_length' => 'Nomor telepon maksimal 15 karakter'
        ],
        'alamat' => [
            'required' => 'Alamat harus diisi',
            'max_length' => 'Alamat maksimal 150 karakter'
        ]
    ];

    /**
     * Get all customers with filtering and pagination
     */
    public function getAllWithFilter(array $filter = [], int $limit = 15, int $offset = 0, string $orderBy = 'id_pelanggan', string $orderType = 'ASC'): array
    {
        $builder = $this->builder();

        // Apply keyword filter
        if (!empty($filter['keyword'])) {
            $keyword = strtolower($filter['keyword']);
            $builder->groupStart()
                    ->like('LOWER(id_pelanggan)', $keyword)
                    ->orLike('LOWER(nama)', $keyword)
                    ->orLike('LOWER(telepon)', $keyword)
                    ->orLike('LOWER(alamat)', $keyword)
                    ->groupEnd();
        }

        // Get total count
        $total = $builder->countAllResults(false);

        // Apply ordering and pagination
        $builder->orderBy($orderBy, $orderType);
        
        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        $data = $builder->get()->getResult($this->returnType);

        return [$data, $total];
    }

    /**
     * Get customer by field
     */
    public function getByField(string $field, string $value): ?PelangganEntity
    {
        return $this->where($field, $value)->first();
    }

    /**
     * Check if customer name exists (excluding current ID)
     */
    public function isNameExists(string $name, string $excludeId = ''): bool
    {
        $builder = $this->builder();
        $builder->where('LOWER(nama)', strtolower($name));
        
        if (!empty($excludeId)) {
            $builder->where('id_pelanggan !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Check if customer is being used in shipments
     */
    public function isInUse(string $id): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pengiriman');
        
        return $builder->where('id_pelanggan', $id)->countAllResults() > 0;
    }

    /**
     * Get the last customer ID for auto-generation
     */
    public function getLastId(): ?string
    {
        $result = $this->orderBy('id_pelanggan', 'DESC')->first();
        return $result ? $result->id_pelanggan : null;
    }

    /**
     * Generate next customer ID
     */
    public function generateNextId(): string
    {
        $lastId = $this->getLastId();
        return PelangganEntity::generateNextId($lastId);
    }

    /**
     * Save customer with validation
     */
    public function savePelanggan(array $data, string $id = ''): bool
    {
        if (empty($id)) {
            // Insert new customer
            return $this->insert($data) !== false;
        } else {
            // Update existing customer
            return $this->update($id, $data);
        }
    }

    /**
     * Delete customer if not in use
     */
    public function deletePelanggan(string $id): array
    {
        $result = ['success' => false, 'message' => ''];

        // Check if customer exists
        $pelanggan = $this->find($id);
        if (!$pelanggan) {
            $result['message'] = 'Pelanggan tidak ditemukan';
            return $result;
        }

        // Check if customer is in use
        if ($this->isInUse($id)) {
            $result['message'] = 'Pelanggan sedang digunakan dalam pengiriman dan tidak dapat dihapus';
            return $result;
        }

        // Delete customer
        if ($this->delete($id)) {
            $result['success'] = true;
            $result['message'] = 'Pelanggan berhasil dihapus';
        } else {
            $result['message'] = 'Gagal menghapus pelanggan';
        }

        return $result;
    }

    /**
     * Get customers for dropdown/select options
     */
    public function getCustomersForSelect(): array
    {
        $customers = $this->orderBy('nama', 'ASC')->findAll();
        $options = [];
        
        foreach ($customers as $customer) {
            $options[$customer->id_pelanggan] = $customer->nama . ' (' . $customer->telepon . ')';
        }
        
        return $options;
    }

    /**
     * Check if phone number exists (excluding current ID)
     */
    public function isPhoneExists(string $phone, string $excludeId = ''): bool
    {
        $builder = $this->builder();
        $builder->where('telepon', $phone);
        
        if (!empty($excludeId)) {
            $builder->where('id_pelanggan !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get customer statistics
     */
    public function getCustomerStatistics(): array
    {
        $totalCustomers = $this->countAllResults();
        
        // Get customers by type (simple heuristic based on name)
        $corporateCount = 0;
        $individualCount = 0;
        
        $customers = $this->findAll();
        foreach ($customers as $customer) {
            if ($customer->getCustomerType() === 'Corporate') {
                $corporateCount++;
            } else {
                $individualCount++;
            }
        }

        return [
            'total_customers' => $totalCustomers,
            'corporate_count' => $corporateCount,
            'individual_count' => $individualCount
        ];
    }

    /**
     * Search customers by keyword
     */
    public function searchCustomers(string $keyword, int $limit = 10): array
    {
        $builder = $this->builder();
        
        $builder->groupStart()
                ->like('LOWER(nama)', strtolower($keyword))
                ->orLike('LOWER(telepon)', strtolower($keyword))
                ->orLike('LOWER(alamat)', strtolower($keyword))
                ->groupEnd();
                
        $builder->orderBy('nama', 'ASC');
        $builder->limit($limit);
        
        return $builder->get()->getResult($this->returnType);
    }
}