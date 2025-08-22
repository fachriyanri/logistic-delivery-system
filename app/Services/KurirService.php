<?php

namespace App\Services;

use App\Models\KurirModel;
use App\Entities\KurirEntity;

class KurirService
{
    protected KurirModel $kurirModel;

    public function __construct()
    {
        $this->kurirModel = new KurirModel();
    }

    /**
     * Get all couriers with filtering and pagination
     */
    public function getAllCouriers(array $filter = [], int $limit = 15, int $offset = 0, string $orderBy = 'id_kurir', string $orderType = 'ASC'): array
    {
        return $this->kurirModel->getAllWithFilter($filter, $limit, $offset, $orderBy, $orderType);
    }

    /**
     * Get courier by ID
     */
    public function getCourierById(string $id): ?KurirEntity
    {
        return $this->kurirModel->find($id);
    }

    /**
     * Create new courier
     */
    public function createCourier(array $data): array
    {
        $result = ['success' => false, 'message' => '', 'data' => null];

        try {
            // Generate ID if not provided
            if (empty($data['id_kurir'])) {
                $data['id_kurir'] = $this->kurirModel->generateNextId();
            }

            // Validate data
            $validationErrors = $this->validateCourierData($data);
            if (!empty($validationErrors)) {
                $result['message'] = implode(', ', $validationErrors);
                return $result;
            }

            // Check if ID already exists
            if ($this->kurirModel->find($data['id_kurir'])) {
                $result['message'] = 'ID Kurir sudah terdaftar';
                return $result;
            }

            // Check if phone number already exists
            if ($this->kurirModel->isPhoneExists($data['telepon'])) {
                $result['message'] = 'Nomor telepon sudah terdaftar';
                return $result;
            }

            // Save courier
            if ($this->kurirModel->saveKurir($data)) {
                $result['success'] = true;
                $result['message'] = 'Kurir berhasil dibuat';
                $result['data'] = $this->kurirModel->find($data['id_kurir']);
            } else {
                $result['message'] = 'Gagal menyimpan kurir';
            }

        } catch (\Exception $e) {
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Update existing courier
     */
    public function updateCourier(string $id, array $data): array
    {
        $result = ['success' => false, 'message' => '', 'data' => null];

        try {
            // Check if courier exists
            $existingCourier = $this->kurirModel->find($id);
            if (!$existingCourier) {
                $result['message'] = 'Kurir tidak ditemukan';
                return $result;
            }

            // Remove id_kurir from update data to prevent changing primary key
            unset($data['id_kurir']);

            // Validate data
            $data['id_kurir'] = $id; // Add for validation context
            $validationErrors = $this->validateCourierData($data, $id);
            if (!empty($validationErrors)) {
                $result['message'] = implode(', ', $validationErrors);
                return $result;
            }
            unset($data['id_kurir']); // Remove again for update

            // Check if phone number already exists (excluding current record)
            if (isset($data['telepon']) && $this->kurirModel->isPhoneExists($data['telepon'], $id)) {
                $result['message'] = 'Nomor telepon sudah terdaftar';
                return $result;
            }

            // Update courier
            if ($this->kurirModel->saveKurir($data, $id)) {
                $result['success'] = true;
                $result['message'] = 'Kurir berhasil diperbarui';
                $result['data'] = $this->kurirModel->find($id);
            } else {
                $result['message'] = 'Gagal memperbarui kurir';
            }

        } catch (\Exception $e) {
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Delete courier
     */
    public function deleteCourier(string $id): array
    {
        return $this->kurirModel->deleteKurir($id);
    }

    /**
     * Generate next courier ID
     */
    public function generateNextId(): string
    {
        return $this->kurirModel->generateNextId();
    }

    /**
     * Get couriers for dropdown/select options
     */
    public function getCouriersForSelect(): array
    {
        return $this->kurirModel->getCouriersForSelect();
    }

    /**
     * Authenticate courier
     */
    public function authenticateCourier(string $username, string $password): ?KurirEntity
    {
        return $this->kurirModel->authenticate($username, $password);
    }

    /**
     * Update courier password
     */
    public function updatePassword(string $id, string $newPassword): array
    {
        $result = ['success' => false, 'message' => ''];

        try {
            // Check if courier exists
            $courier = $this->kurirModel->find($id);
            if (!$courier) {
                $result['message'] = 'Kurir tidak ditemukan';
                return $result;
            }

            // Validate password
            if (strlen($newPassword) < 6) {
                $result['message'] = 'Password minimal 6 karakter';
                return $result;
            }

            // Update password
            if ($this->kurirModel->updatePassword($id, $newPassword)) {
                $result['success'] = true;
                $result['message'] = 'Password berhasil diperbarui';
            } else {
                $result['message'] = 'Gagal memperbarui password';
            }

        } catch (\Exception $e) {
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Validate courier data
     */
    public function validateCourierData(array $data, string $id = ''): array
    {
        $errors = [];

        // Validate required fields
        if (empty($data['nama'])) {
            $errors[] = 'Nama kurir harus diisi';
        }

        if (empty($data['jenis_kelamin'])) {
            $errors[] = 'Jenis kelamin harus dipilih';
        }

        if (empty($data['telepon'])) {
            $errors[] = 'Nomor telepon harus diisi';
        }

        // Validate password for new courier
        if (empty($id) && empty($data['password'])) {
            $errors[] = 'Password harus diisi';
        }

        // Validate ID format if provided
        if (!empty($data['id_kurir'])) {
            if (strlen($data['id_kurir']) > 5) {
                $errors[] = 'ID Kurir maksimal 5 karakter';
            }
            if (!preg_match('/^KRR\d{2}$/', $data['id_kurir'])) {
                $errors[] = 'Format ID Kurir harus KRRxx (contoh: KRR01)';
            }
        }

        // Validate field lengths
        if (!empty($data['nama']) && strlen($data['nama']) > 30) {
            $errors[] = 'Nama kurir maksimal 30 karakter';
        }

        if (!empty($data['telepon']) && strlen($data['telepon']) > 15) {
            $errors[] = 'Nomor telepon maksimal 15 karakter';
        }

        if (!empty($data['alamat']) && strlen($data['alamat']) > 150) {
            $errors[] = 'Alamat maksimal 150 karakter';
        }

        // Validate gender
        if (!empty($data['jenis_kelamin']) && !in_array($data['jenis_kelamin'], ['Laki-Laki', 'Perempuan'])) {
            $errors[] = 'Jenis kelamin tidak valid';
        }

        // Validate phone number format
        if (!empty($data['telepon']) && !preg_match('/^[0-9+\-\s()]+$/', $data['telepon'])) {
            $errors[] = 'Format nomor telepon tidak valid';
        }

        // Validate password length
        if (!empty($data['password']) && strlen($data['password']) < 6) {
            $errors[] = 'Password minimal 6 karakter';
        }

        return $errors;
    }

    /**
     * Check if courier can be deleted
     */
    public function canDelete(string $id): bool
    {
        return !$this->kurirModel->isInUse($id);
    }

    /**
     * Search couriers by keyword
     */
    public function searchCouriers(string $keyword, int $limit = 10): array
    {
        $filter = ['keyword' => $keyword];
        [$couriers, $total] = $this->kurirModel->getAllWithFilter($filter, $limit, 0, 'nama', 'ASC');
        
        return $couriers;
    }

    /**
     * Get courier statistics
     */
    public function getCourierStatistics(): array
    {
        return $this->kurirModel->getCourierStatistics();
    }

    /**
     * Get gender options for forms
     */
    public function getGenderOptions(): array
    {
        return KurirEntity::getGenderOptions();
    }
}