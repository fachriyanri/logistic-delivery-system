<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\KurirEntity;

class KurirModel extends Model
{
    protected $table = 'kurir';
    protected $primaryKey = 'id_kurir';
    protected $returnType = KurirEntity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_kurir',
        'nama',
        'jenis_kelamin',
        'telepon',
        'alamat',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'id_kurir' => 'required|max_length[5]',
        'nama' => 'required|max_length[30]',
        'jenis_kelamin' => 'required|in_list[Laki-Laki,Perempuan]',
        'telepon' => 'required|max_length[15]',
        'alamat' => 'permit_empty|max_length[150]',
        'password' => 'permit_empty' // Allow empty password temporarily
    ];

    protected $validationMessages = [
        'id_kurir' => [
            'required' => 'ID Kurir harus diisi',
            'max_length' => 'ID Kurir maksimal 5 karakter'
        ],
        'nama' => [
            'required' => 'Nama kurir harus diisi',
            'max_length' => 'Nama kurir maksimal 30 karakter'
        ],
        'jenis_kelamin' => [
            'required' => 'Jenis kelamin harus dipilih',
            'in_list' => 'Jenis kelamin tidak valid'
        ],
        'telepon' => [
            'required' => 'Nomor telepon harus diisi',
            'max_length' => 'Nomor telepon maksimal 15 karakter'
        ],
        'alamat' => [
            'max_length' => 'Alamat maksimal 150 karakter'
        ],
        'password' => []
    ];

    /**
     * Get all couriers with filtering and pagination
     */
    public function getAllWithFilter(array $filter = [], int $limit = 15, int $offset = 0, string $orderBy = 'id_kurir', string $orderType = 'ASC'): array
    {
        $builder = $this->builder();

        // Apply keyword filter
        if (!empty($filter['keyword'])) {
            $keyword = strtolower($filter['keyword']);
            $builder->groupStart()
                    ->like('LOWER(id_kurir)', $keyword)
                    ->orLike('LOWER(nama)', $keyword)
                    ->orLike('LOWER(telepon)', $keyword)
                    ->orLike('LOWER(jenis_kelamin)', $keyword)
                    ->orLike('LOWER(alamat)', $keyword)
                    ->groupEnd();
        }

        // Apply gender filter
        if (!empty($filter['jenis_kelamin'])) {
            $builder->where('jenis_kelamin', $filter['jenis_kelamin']);
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
     * Get courier by field
     */
    public function getByField(string $field, string $value): ?KurirEntity
    {
        return $this->where($field, $value)->first();
    }

    /**
     * Check if courier is being used in shipments
     */
    public function isInUse(string $id): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pengiriman');
        
        return $builder->where('id_kurir', $id)->countAllResults() > 0;
    }

    /**
     * Get the last courier ID for auto-generation
     */
    public function getLastId(): ?string
    {
        $result = $this->orderBy('id_kurir', 'DESC')->first();
        return $result ? $result->id_kurir : null;
    }

    /**
     * Generate next courier ID
     */
    public function generateNextId(): string
    {
        $lastId = $this->getLastId();
        return KurirEntity::generateNextId($lastId);
    }

    /**
     * Save courier with validation
     */
    public function saveKurir(array $data, string $id = ''): bool
    {
        // Debug: Log the data being saved
        log_message('debug', 'KurirModel::saveKurir - Data: ' . json_encode($data));
        log_message('debug', 'KurirModel::saveKurir - ID: ' . $id);
        
        // Ensure timestamps are set
        $currentTime = date('Y-m-d H:i:s');
        
        try {
            if (empty($id)) {
                // Insert new courier - set both created_at and updated_at
                if (!isset($data['created_at'])) {
                    $data['created_at'] = $currentTime;
                }
                if (!isset($data['updated_at'])) {
                    $data['updated_at'] = $currentTime;
                }
                
                log_message('debug', 'KurirModel::saveKurir - Insert data with timestamps: ' . json_encode($data));
                $result = $this->insert($data);
                if ($result === false) {
                    log_message('error', 'KurirModel::saveKurir - Insert failed. Errors: ' . json_encode($this->errors()));
                }
                return $result !== false;
            } else {
                // Update existing courier - only set updated_at
                if (!isset($data['updated_at'])) {
                    $data['updated_at'] = $currentTime;
                }
                
                log_message('debug', 'KurirModel::saveKurir - Update data with timestamps: ' . json_encode($data));
                $result = $this->update($id, $data);
                if ($result === false) {
                    log_message('error', 'KurirModel::saveKurir - Update failed. Errors: ' . json_encode($this->errors()));
                }
                return $result;
            }
        } catch (\Exception $e) {
            log_message('error', 'KurirModel::saveKurir - Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete courier if not in use
     */
    public function deleteKurir(string $id): array
    {
        $result = ['success' => false, 'message' => ''];

        // Check if courier exists
        $kurir = $this->find($id);
        if (!$kurir) {
            $result['message'] = 'Kurir tidak ditemukan';
            return $result;
        }

        // Check if courier is in use
        if ($this->isInUse($id)) {
            $result['message'] = 'Kurir sedang digunakan dalam pengiriman dan tidak dapat dihapus';
            return $result;
        }

        // Delete courier
        if ($this->delete($id)) {
            $result['success'] = true;
            $result['message'] = 'Kurir berhasil dihapus';
        } else {
            $result['message'] = 'Gagal menghapus kurir';
        }

        return $result;
    }

    /**
     * Get couriers for dropdown/select options
     */
    public function getCouriersForSelect(): array
    {
        $couriers = $this->orderBy('nama', 'ASC')->findAll();
        $options = [];
        
        foreach ($couriers as $courier) {
            $options[$courier->id_kurir] = $courier->nama . ' (' . $courier->telepon . ')';
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
            $builder->where('id_kurir !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get courier statistics
     */
    public function getCourierStatistics(): array
    {
        $totalCouriers = $this->countAllResults();
        
        // Get couriers by gender
        $maleCount = $this->where('jenis_kelamin', 'Laki-Laki')->countAllResults(false);
        $femaleCount = $this->where('jenis_kelamin', 'Perempuan')->countAllResults(false);

        return [
            'total_couriers' => $totalCouriers,
            'male_count' => $maleCount,
            'female_count' => $femaleCount
        ];
    }
}