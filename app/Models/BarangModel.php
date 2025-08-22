<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\BarangEntity;

class BarangModel extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    protected $returnType = BarangEntity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_barang',
        'nama',
        'satuan',
        'del_no',
        'id_kategori'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_barang' => 'required|max_length[7]|is_unique[barang.id_barang,id_barang,{id_barang}]',
        'nama' => 'required|max_length[30]|is_unique[barang.nama,id_barang,{id_barang}]',
        'satuan' => 'required|max_length[20]',
        'del_no' => 'required|max_length[15]',
        'id_kategori' => 'required|max_length[5]|is_not_unique[kategori.id_kategori]'
    ];

    protected $validationMessages = [
        'id_barang' => [
            'required' => 'ID Barang harus diisi',
            'max_length' => 'ID Barang maksimal 7 karakter',
            'is_unique' => 'ID Barang sudah terdaftar'
        ],
        'nama' => [
            'required' => 'Nama barang harus diisi',
            'max_length' => 'Nama barang maksimal 30 karakter',
            'is_unique' => 'Nama barang sudah terdaftar'
        ],
        'satuan' => [
            'required' => 'Satuan harus diisi',
            'max_length' => 'Satuan maksimal 20 karakter'
        ],
        'del_no' => [
            'required' => 'Delivery Number harus diisi',
            'max_length' => 'Delivery Number maksimal 15 karakter'
        ],
        'id_kategori' => [
            'required' => 'Kategori harus dipilih',
            'max_length' => 'ID Kategori maksimal 5 karakter',
            'is_not_unique' => 'Kategori tidak valid'
        ]
    ];

    /**
     * Get all items with category information and filtering
     */
    public function getAllWithFilter(array $filter = [], int $limit = 15, int $offset = 0, string $orderBy = 'id_barang', string $orderType = 'ASC'): array
    {
        $builder = $this->db->table($this->table . ' b');
        $builder->select('b.*, k.nama as kategori_nama, k.keterangan as kategori_keterangan');
        $builder->join('kategori k', 'k.id_kategori = b.id_kategori', 'left');

        // Apply keyword filter
        if (!empty($filter['keyword'])) {
            $keyword = strtolower($filter['keyword']);
            $builder->groupStart()
                    ->like('LOWER(b.id_barang)', $keyword)
                    ->orLike('LOWER(b.nama)', $keyword)
                    ->orLike('LOWER(b.satuan)', $keyword)
                    ->orLike('LOWER(b.del_no)', $keyword)
                    ->orLike('LOWER(k.nama)', $keyword)
                    ->orLike('LOWER(k.id_kategori)', $keyword)
                    ->groupEnd();
        }

        // Apply category filter
        if (!empty($filter['id_kategori'])) {
            $builder->where('b.id_kategori', $filter['id_kategori']);
        }

        // Exclude specific items if provided
        if (!empty($filter['exclude_items']) && is_array($filter['exclude_items'])) {
            $builder->whereNotIn('b.id_barang', $filter['exclude_items']);
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
            $entity = new BarangEntity($result);
            $entities[] = $entity;
        }

        return [$entities, $total];
    }

    /**
     * Get item by field with category information
     */
    public function getByFieldWithCategory(string $field, string $value): ?BarangEntity
    {
        $builder = $this->db->table($this->table . ' b');
        $builder->select('b.*, k.nama as kategori_nama, k.keterangan as kategori_keterangan');
        $builder->join('kategori k', 'k.id_kategori = b.id_kategori', 'left');
        $builder->where($field, $value);
        
        $result = $builder->get()->getRowArray();
        
        return $result ? new BarangEntity($result) : null;
    }

    /**
     * Check if item name exists (excluding current ID)
     */
    public function isNameExists(string $name, string $excludeId = ''): bool
    {
        $builder = $this->builder();
        $builder->where('LOWER(nama)', strtolower($name));
        
        if (!empty($excludeId)) {
            $builder->where('id_barang !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Check if item is being used in shipments
     */
    public function isInUse(string $id): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('detail_pengiriman');
        
        return $builder->where('id_barang', $id)->countAllResults() > 0;
    }

    /**
     * Get the last item ID for auto-generation
     */
    public function getLastId(): ?string
    {
        $result = $this->orderBy('id_barang', 'DESC')->first();
        return $result ? $result->id_barang : null;
    }

    /**
     * Generate next item ID
     */
    public function generateNextId(): string
    {
        $lastId = $this->getLastId();
        return BarangEntity::generateNextId($lastId);
    }

    /**
     * Save item with validation
     */
    public function saveBarang(array $data, string $id = ''): bool
    {
        if (empty($id)) {
            // Insert new item
            return $this->insert($data) !== false;
        } else {
            // Update existing item
            return $this->update($id, $data);
        }
    }

    /**
     * Delete item if not in use
     */
    public function deleteBarang(string $id): array
    {
        $result = ['success' => false, 'message' => ''];

        // Check if item exists
        $barang = $this->find($id);
        if (!$barang) {
            $result['message'] = 'Barang tidak ditemukan';
            return $result;
        }

        // Check if item is in use
        if ($this->isInUse($id)) {
            $result['message'] = 'Barang sedang digunakan dalam pengiriman dan tidak dapat dihapus';
            return $result;
        }

        // Delete item
        if ($this->delete($id)) {
            $result['success'] = true;
            $result['message'] = 'Barang berhasil dihapus';
        } else {
            $result['message'] = 'Gagal menghapus barang';
        }

        return $result;
    }

    /**
     * Get items for dropdown/select options
     */
    public function getItemsForSelect(string $categoryId = ''): array
    {
        $builder = $this->builder();
        
        if (!empty($categoryId)) {
            $builder->where('id_kategori', $categoryId);
        }
        
        $items = $builder->orderBy('nama', 'ASC')->get()->getResult($this->returnType);
        $options = [];
        
        foreach ($items as $item) {
            $options[$item->id_barang] = $item->nama . ' (' . $item->satuan . ')';
        }
        
        return $options;
    }

    /**
     * Get items by category
     */
    public function getByCategory(string $categoryId): array
    {
        return $this->where('id_kategori', $categoryId)
                   ->orderBy('nama', 'ASC')
                   ->findAll();
    }
}