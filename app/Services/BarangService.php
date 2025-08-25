<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Entities\BarangEntity;

class BarangService
{
    protected BarangModel $barangModel;
    protected KategoriModel $kategoriModel;
    protected BaseConnection $db;

    public function __construct()
    {
        $this->barangModel = model('BarangModel');
        $this->kategoriModel = model('KategoriModel');
        $this->db = \Config\Database::connect();
    }

    /**
     * Get all items with filtering and pagination
     */
    public function getAllItems(array $filter = [], int $limit = 15, int $offset = 0, string $orderBy = 'id_barang', string $orderType = 'ASC'): array
    {
        return $this->barangModel->getAllWithFilter($filter, $limit, $offset, $orderBy, $orderType);
    }

    /**
     * Get item by ID with category information
     */
    public function getItemById(string $id): ?BarangEntity
    {
        return $this->barangModel->getByFieldWithCategory('b.id_barang', $id);
    }

    /**
     * Create new item
     */
    public function createItem(array $data): array
    {
        $result = ['success' => false, 'message' => '', 'data' => null];

        try {
            // Generate ID if not provided
            if (empty($data['id_barang'])) {
                $data['id_barang'] = $this->barangModel->generateNextId();
            }

            // Use CodeIgniter's validation library
            $validation = \Config\Services::validation();

            // Define the validation rules
            $validation->setRules([
                'id_barang' => 'required|max_length[7]|regex_match[/^BRG\d{4}$/]|is_unique[barang.id_barang]',
                'nama'      => 'required|max_length[30]|is_unique[barang.nama]',
                'satuan'    => 'required|max_length[20]',
                'harga'     => 'permit_empty|decimal|greater_than_equal_to[0]',
                'id_kategori' => 'required|max_length[5]|is_not_unique[kategori.id_kategori]'
            ]);

            if (!$validation->run($data)) {
                $result['message'] = implode(', ', $validation->getErrors());
                return $result;
            }

            // Ensure harga has a default value
            if (!isset($data['harga']) || $data['harga'] === '') {
                $data['harga'] = 0.00;
            }

            // Debug logging (remove in production)
            log_message('debug', 'BarangService::createItem - Data to insert: ' . json_encode($data));

            // Use the model to insert (unified approach)
            $insertResult = $this->barangModel->insert($data);
            
            if ($insertResult !== false) {
                $result['success'] = true;
                $result['message'] = 'Barang berhasil dibuat';
                $result['data'] = $this->getItemById($data['id_barang']);
            } else {
                // Get detailed error information
                $modelErrors = $this->barangModel->errors();
                $dbError = $this->db->error();
                
                $errorMessage = 'Gagal menyimpan barang';
                
                if (!empty($modelErrors)) {
                    $errorMessage .= ': ' . implode(', ', $modelErrors);
                } elseif (!empty($dbError['message'])) {
                    $errorMessage .= ': ' . $dbError['message'];
                    
                    // Check if it's a column doesn't exist error
                    if (strpos($dbError['message'], 'harga') !== false && strpos($dbError['message'], "doesn't exist") !== false) {
                        $errorMessage .= '. Kolom harga belum ada di database. Jalankan migration dengan: php spark migrate';
                    }
                }
                
                $result['message'] = $errorMessage;
            }
        } catch (\Exception $e) {
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Update existing item
     */
    public function updateItem(string $id, array $data): array
    {
        $result = ['success' => false, 'message' => '', 'data' => null];

        try {
            // Check if item exists
            $existingItem = $this->barangModel->find($id);
            if (!$existingItem) {
                $result['message'] = 'Barang tidak ditemukan';
                return $result;
            }

            // Remove id_barang from update data to prevent changing primary key
            unset($data['id_barang']);

            // Use CodeIgniter's validation library
            $validation = \Config\Services::validation();

            // Define the validation rules for update
            $validation->setRules([
                'nama'      => 'permit_empty|max_length[30]|is_unique[barang.nama,id_barang,' . $id . ']',
                'satuan'    => 'permit_empty|max_length[20]',
                'harga'     => 'permit_empty|decimal|greater_than_equal_to[0]',
                'id_kategori' => 'permit_empty|max_length[5]|is_not_unique[kategori.id_kategori]'
            ]);

            if (!$validation->run($data)) {
                $result['message'] = implode(', ', $validation->getErrors());
                return $result;
            }

            // Ensure harga has a default value if provided but empty
            if (isset($data['harga']) && $data['harga'] === '') {
                $data['harga'] = 0.00;
            }

            // Update item using model
            if ($this->barangModel->update($id, $data)) {
                $result['success'] = true;
                $result['message'] = 'Barang berhasil diperbarui';
                $result['data'] = $this->getItemById($id);
            } else {
                $result['message'] = 'Gagal memperbarui barang: ' . implode(', ', $this->barangModel->errors());
            }
        } catch (\Exception $e) {
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Delete item
     */
    public function deleteItem(string $id): array
    {
        return $this->barangModel->deleteBarang($id);
    }

    /**
     * Generate next item ID
     */
    public function generateNextId(): string
    {
        return $this->barangModel->generateNextId();
    }

    /**
     * Get items for dropdown/select options
     */
    public function getItemsForSelect(string $categoryId = ''): array
    {
        return $this->barangModel->getItemsForSelect($categoryId);
    }

    /**
     * Get items by category
     */
    public function getItemsByCategory(string $categoryId): array
    {
        return $this->barangModel->getByCategory($categoryId);
    }

    /**
     * Get categories for dropdown
     */
    public function getCategoriesForSelect(): array
    {
        $categories = $this->kategoriModel->orderBy('nama', 'ASC')->findAll();
        $options = [];

        foreach ($categories as $category) {
            $options[$category->id_kategori] = $category->nama;
        }

        return $options;
    }

    /**
     * Validate item data (legacy method for backward compatibility)
     */
    public function validateItemData(array $data, string $id = ''): array
    {
        $errors = [];

        // Validate required fields
        if (empty($data['nama'])) {
            $errors[] = 'Nama barang harus diisi';
        }

        if (empty($data['satuan'])) {
            $errors[] = 'Satuan harus diisi';
        }

        if (empty($data['id_kategori'])) {
            $errors[] = 'Kategori harus dipilih';
        }

        // Validate ID format if provided
        if (!empty($data['id_barang'])) {
            if (strlen($data['id_barang']) > 7) {
                $errors[] = 'ID Barang maksimal 7 karakter';
            }
            if (!preg_match('/^BRG\d{4}$/', $data['id_barang'])) {
                $errors[] = 'Format ID Barang harus BRGxxxx (contoh: BRG0001)';
            }
        }

        // Validate field lengths
        if (!empty($data['nama']) && strlen($data['nama']) > 30) {
            $errors[] = 'Nama barang maksimal 30 karakter';
        }

        if (!empty($data['satuan']) && strlen($data['satuan']) > 20) {
            $errors[] = 'Satuan maksimal 20 karakter';
        }

        if (!empty($data['id_kategori']) && strlen($data['id_kategori']) > 5) {
            $errors[] = 'ID Kategori maksimal 5 karakter';
        }

        // Validate harga
        if (isset($data['harga'])) {
            if (!is_numeric($data['harga']) && $data['harga'] !== '') {
                $errors[] = 'Harga harus berupa angka';
            }
            if (is_numeric($data['harga']) && $data['harga'] < 0) {
                $errors[] = 'Harga tidak boleh negatif';
            }
        }

        return $errors;
    }

    /**
     * Check if item can be deleted
     */
    public function canDelete(string $id): bool
    {
        return !$this->barangModel->isInUse($id);
    }

    /**
     * Search items by keyword
     */
    public function searchItems(string $keyword, int $limit = 10): array
    {
        $filter = ['keyword' => $keyword];
        [$items, $total] = $this->barangModel->getAllWithFilter($filter, $limit, 0, 'nama', 'ASC');

        return $items;
    }

    /**
     * Get item statistics
     */
    public function getItemStatistics(): array
    {
        $totalItems = $this->barangModel->countAllResults();

        // One query to get all counts, grouped by category
        $counts = $this->barangModel
            ->select('kategori.nama as category, COUNT(barang.id_barang) as count')
            ->join('kategori', 'kategori.id_kategori = barang.id_kategori')
            ->groupBy('kategori.id_kategori')
            ->findAll();

        return [
            'total_items' => $totalItems,
            'categories_with_count' => $counts
        ];
    }
}
