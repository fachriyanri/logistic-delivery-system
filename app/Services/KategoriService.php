<?php

namespace App\Services;

use App\Models\KategoriModel;
use App\Entities\KategoriEntity;

class KategoriService
{
    protected KategoriModel $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    /**
     * Get all categories with filtering and pagination
     */
    public function getAllCategories(array $filter = [], int $limit = 15, int $offset = 0, string $orderBy = 'id_kategori', string $orderType = 'ASC'): array
    {
        return $this->kategoriModel->getAllWithFilter($filter, $limit, $offset, $orderBy, $orderType);
    }

    /**
     * Get category by ID
     */
    public function getCategoryById(string $id): ?KategoriEntity
    {
        return $this->kategoriModel->find($id);
    }

    /**
     * Check if category name exists (excluding current ID)
     */
    public function getCategoryByName(string $name, string $excludeId = ''): bool
    {
        return $this->kategoriModel->isNameExists($name, $excludeId);
    }

    /**
     * Create new category
     */
    public function createCategory(array $data): array
    {
        $result = ['success' => false, 'message' => '', 'data' => null];

        try {
            // Generate ID if not provided
            if (empty($data['id_kategori'])) {
                $data['id_kategori'] = $this->kategoriModel->generateNextId();
            }

            // Check if ID already exists using direct database query
            $db = \Config\Database::connect();
            $existingCount = $db->table('kategori')->where('id_kategori', $data['id_kategori'])->countAllResults();
            if ($existingCount > 0) {
                $result['message'] = 'ID Kategori sudah terdaftar';
                return $result;
            }

            // Check if name already exists using direct database query
            $existingNameCount = $db->table('kategori')->where('LOWER(nama)', strtolower($data['nama']))->countAllResults();
            if ($existingNameCount > 0) {
                $result['message'] = 'Nama kategori sudah terdaftar';
                return $result;
            }

            // Save category using the new method that bypasses entity issues
            if ($this->kategoriModel->insertCategory($data)) {
                $result['success'] = true;
                $result['message'] = 'Kategori berhasil dibuat';
                $result['data'] = $this->kategoriModel->find($data['id_kategori']);
            } else {
                $result['message'] = 'Gagal menyimpan kategori ke database';
            }

        } catch (\Exception $e) {
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Update existing category
     */
    public function updateCategory(string $id, array $data): array
    {
        $result = ['success' => false, 'message' => '', 'data' => null];

        try {
            // Check if category exists
            $existingCategory = $this->kategoriModel->find($id);
            if (!$existingCategory) {
                $result['message'] = 'Kategori tidak ditemukan';
                return $result;
            }

            // Check if name already exists (excluding current record)
            if (isset($data['nama']) && $this->kategoriModel->isNameExists($data['nama'], $id)) {
                $result['message'] = 'Nama kategori sudah terdaftar';
                return $result;
            }

            // If ID is being changed, check if new ID exists
            if (isset($data['id_kategori']) && $data['id_kategori'] !== $id) {
                if ($this->kategoriModel->find($data['id_kategori'])) {
                    $result['message'] = 'ID Kategori baru sudah terdaftar';
                    return $result;
                }
            }

            // Prepare update data (exclude id_kategori for safety)
            $updateData = [
                'nama' => $data['nama'],
                'keterangan' => $data['keterangan'] ?? ''
            ];

            // Update category
            if ($this->kategoriModel->update($id, $updateData)) {
                $result['success'] = true;
                $result['message'] = 'Kategori berhasil diperbarui';
                $result['data'] = $this->kategoriModel->find($id);
            } else {
                $errors = $this->kategoriModel->errors();
                $result['message'] = !empty($errors) ? implode(', ', $errors) : 'Gagal memperbarui kategori';
            }

        } catch (\Exception $e) {
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Delete category
     */
    public function deleteCategory(string $id): array
    {
        return $this->kategoriModel->deleteKategori($id);
    }

    /**
     * Generate next category ID
     */
    public function generateNextId(): string
    {
        return $this->kategoriModel->generateNextId();
    }

    /**
     * Get categories for dropdown/select options
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
     * Validate category data
     */
    public function validateCategoryData(array $data, string $id = ''): array
    {
        $errors = [];

        // Validate required fields
        if (empty(trim($data['nama'] ?? ''))) {
            $errors[] = 'Nama kategori harus diisi';
        }

        if (empty(trim($data['id_kategori'] ?? ''))) {
            $errors[] = 'ID Kategori harus diisi';
        }

        // Validate ID format if provided
        if (!empty($data['id_kategori'])) {
            if (strlen($data['id_kategori']) > 5) {
                $errors[] = 'ID Kategori maksimal 5 karakter';
            }
            if (!preg_match('/^KTG\d{2}$/', $data['id_kategori'])) {
                $errors[] = 'Format ID Kategori harus KTGxx (contoh: KTG01)';
            }
        }

        // Validate name length
        if (!empty($data['nama']) && strlen($data['nama']) > 30) {
            $errors[] = 'Nama kategori maksimal 30 karakter';
        }

        // Validate description length
        if (!empty($data['keterangan']) && strlen($data['keterangan']) > 150) {
            $errors[] = 'Keterangan maksimal 150 karakter';
        }

        return $errors;
    }

    /**
     * Check if category can be deleted
     */
    public function canDelete(string $id): bool
    {
        return !$this->kategoriModel->isInUse($id);
    }
}