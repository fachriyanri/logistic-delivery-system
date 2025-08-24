<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\KategoriEntity;

class KategoriModel extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';
    protected $returnType = KategoriEntity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_kategori',
        'nama',
        'keterangan'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'id_kategori' => 'required|max_length[5]',
        'nama' => 'required|max_length[30]',
        'keterangan' => 'permit_empty|max_length[150]'
    ];

    protected $validationMessages = [
        'id_kategori' => [
            'required' => 'ID Kategori harus diisi',
            'max_length' => 'ID Kategori maksimal 5 karakter',
            'is_unique' => 'ID Kategori sudah terdaftar'
        ],
        'nama' => [
            'required' => 'Nama kategori harus diisi',
            'max_length' => 'Nama kategori maksimal 30 karakter',
            'is_unique' => 'Nama kategori sudah terdaftar'
        ],
        'keterangan' => [
            'max_length' => 'Keterangan maksimal 150 karakter'
        ]
    ];

    /**
     * Get all categories with filtering and pagination
     */
    public function getAllWithFilter(array $filter = [], int $limit = 15, int $offset = 0, string $orderBy = 'id_kategori', string $orderType = 'ASC'): array
    {
        $builder = $this->builder();

        // Apply keyword filter
        if (!empty($filter['keyword'])) {
            $keyword = strtolower($filter['keyword']);
            $builder->groupStart()
                    ->like('LOWER(id_kategori)', $keyword)
                    ->orLike('LOWER(nama)', $keyword)
                    ->orLike('LOWER(keterangan)', $keyword)
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
     * Get category by field
     */
    public function getByField(string $field, string $value): ?KategoriEntity
    {
        return $this->where($field, $value)->first();
    }

    /**
     * Check if category name exists (excluding current ID)
     */
    public function isNameExists(string $name, string $excludeId = ''): bool
    {
        $builder = $this->builder();
        $builder->where('LOWER(nama)', strtolower($name));
        
        if (!empty($excludeId)) {
            $builder->where('id_kategori !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Check if category is being used by items
     */
    public function isInUse(string $id): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('barang');
        
        return $builder->where('id_kategori', $id)->countAllResults() > 0;
    }

    /**
     * Get the last category ID for auto-generation
     */
    public function getLastId(): ?string
    {
        $result = $this->orderBy('id_kategori', 'DESC')->first();
        return $result ? $result->id_kategori : null;
    }

    /**
     * Generate next category ID
     */
    public function generateNextId(): string
    {
        $lastId = $this->getLastId();
        return KategoriEntity::generateNextId($lastId);
    }

    /**
     * Save category with validation
     */
    public function saveKategori(array $data, string $id = ''): bool
    {
        if (empty($id)) {
            // Insert new category using query builder to bypass entity issues
            return $this->db->table($this->table)->insert($data);
        } else {
            // Update existing category
            return $this->update($id, $data);
        }
    }

    /**
     * Insert category using raw query builder (bypasses entity issues)
     */
    public function insertCategory(array $data): bool
    {
        // Add timestamps if enabled
        if ($this->useTimestamps) {
            $data[$this->createdField] = date('Y-m-d H:i:s');
            $data[$this->updatedField] = date('Y-m-d H:i:s');
        }
        
        return $this->db->table($this->table)->insert($data);
    }

    /**
     * Delete category if not in use
     */
    public function deleteKategori(string $id): array
    {
        $result = ['success' => false, 'message' => ''];

        // Check if category exists using direct database query
        $db = \Config\Database::connect();
        $existingCount = $db->table($this->table)->where('id_kategori', $id)->countAllResults();
        
        if ($existingCount === 0) {
            $result['message'] = 'Kategori tidak ditemukan';
            return $result;
        }

        // Check if category is in use
        if ($this->isInUse($id)) {
            $result['message'] = 'Kategori sedang digunakan dan tidak dapat dihapus';
            return $result;
        }

        // Delete category using direct database query
        if ($db->table($this->table)->where('id_kategori', $id)->delete()) {
            $result['success'] = true;
            $result['message'] = 'Kategori berhasil dihapus';
        } else {
            $result['message'] = 'Gagal menghapus kategori';
        }

        return $result;
    }
}