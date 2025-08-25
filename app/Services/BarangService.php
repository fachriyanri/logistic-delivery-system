<?php

namespace App\Services;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Entities\BarangEntity;

class BarangService
{
    protected BarangModel $barangModel;
    protected KategoriModel $kategoriModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->kategoriModel = new KategoriModel();
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

            // Validate data
            $validationErrors = $this->validateItemData($data);
            if (!empty($validationErrors)) {
                $result['message'] = implode(', ', $validationErrors);
                return $result;
            }

            // Check if ID already exists
            if ($this->barangModel->find($data['id_barang'])) {
                $result['message'] = 'ID Barang sudah terdaftar';
                return $result;
            }

            // Check if name already exists
            if ($this->barangModel->isNameExists($data['nama'])) {
                $result['message'] = 'Nama barang sudah terdaftar';
                return $result;
            }

            // Check if category exists
            if (!$this->kategoriModel->find($data['id_kategori'])) {
                $result['message'] = 'Kategori tidak valid';
                return $result;
            }

            // Save item using direct database insert to bypass model validation
            $db = \Config\Database::connect();
            $builder = $db->table('barang');

            $insertResult = $builder->insert($data);
            if ($insertResult) {
                $result['success'] = true;
                $result['message'] = 'Barang berhasil dibuat';
                $result['data'] = $this->getItemById($data['id_barang']);
            } else {
                $result['message'] = 'Gagal menyimpan barang: ' . $db->error()['message'];
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

            // Validate data
            $data['id_barang'] = $id; // Add for validation context
            $validationErrors = $this->validateItemData($data, $id);
            if (!empty($validationErrors)) {
                $result['message'] = implode(', ', $validationErrors);
                return $result;
            }
            unset($data['id_barang']); // Remove again for update

            // Check if name already exists (excluding current record)
            if (isset($data['nama']) && $this->barangModel->isNameExists($data['nama'], $id)) {
                $result['message'] = 'Nama barang sudah terdaftar';
                return $result;
            }

            // Check if category exists
            if (isset($data['id_kategori']) && !$this->kategoriModel->find($data['id_kategori'])) {
                $result['message'] = 'Kategori tidak valid';
                return $result;
            }

            // Update item
            if ($this->barangModel->update($id, $data)) {
                $result['success'] = true;
                $result['message'] = 'Barang berhasil diperbarui';
                $result['data'] = $this->getItemById($id);
            } else {
                $result['message'] = 'Gagal memperbarui barang';
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
     * Validate item data
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
