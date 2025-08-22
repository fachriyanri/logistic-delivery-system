<?php

namespace App\Services;

use App\Models\PelangganModel;
use App\Entities\PelangganEntity;

class PelangganService
{
    protected PelangganModel $pelangganModel;

    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
    }

    /**
     * Get all customers with filtering and pagination
     */
    public function getAllCustomers(array $filter = [], int $limit = 15, int $offset = 0, string $orderBy = 'id_pelanggan', string $orderType = 'ASC'): array
    {
        return $this->pelangganModel->getAllWithFilter($filter, $limit, $offset, $orderBy, $orderType);
    }

    /**
     * Get customer by ID
     */
    public function getCustomerById(string $id): ?PelangganEntity
    {
        return $this->pelangganModel->find($id);
    }

    /**
     * Create new customer
     */
    public function createCustomer(array $data): array
    {
        $result = ['success' => false, 'message' => '', 'data' => null];

        try {
            // Generate ID if not provided
            if (empty($data['id_pelanggan'])) {
                $data['id_pelanggan'] = $this->pelangganModel->generateNextId();
            }

            // Validate data
            $validationErrors = $this->validateCustomerData($data);
            if (!empty($validationErrors)) {
                $result['message'] = implode(', ', $validationErrors);
                return $result;
            }

            // Check if ID already exists
            if ($this->pelangganModel->find($data['id_pelanggan'])) {
                $result['message'] = 'ID Pelanggan sudah terdaftar';
                return $result;
            }

            // Check if name already exists
            if ($this->pelangganModel->isNameExists($data['nama'])) {
                $result['message'] = 'Nama pelanggan sudah terdaftar';
                return $result;
            }

            // Check if phone number already exists
            if ($this->pelangganModel->isPhoneExists($data['telepon'])) {
                $result['message'] = 'Nomor telepon sudah terdaftar';
                return $result;
            }

            // Save customer
            if ($this->pelangganModel->savePelanggan($data)) {
                $result['success'] = true;
                $result['message'] = 'Pelanggan berhasil dibuat';
                $result['data'] = $this->pelangganModel->find($data['id_pelanggan']);
            } else {
                $result['message'] = 'Gagal menyimpan pelanggan';
            }

        } catch (\Exception $e) {
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Update existing customer
     */
    public function updateCustomer(string $id, array $data): array
    {
        $result = ['success' => false, 'message' => '', 'data' => null];

        try {
            // Check if customer exists
            $existingCustomer = $this->pelangganModel->find($id);
            if (!$existingCustomer) {
                $result['message'] = 'Pelanggan tidak ditemukan';
                return $result;
            }

            // Remove id_pelanggan from update data to prevent changing primary key
            unset($data['id_pelanggan']);

            // Validate data
            $data['id_pelanggan'] = $id; // Add for validation context
            $validationErrors = $this->validateCustomerData($data, $id);
            if (!empty($validationErrors)) {
                $result['message'] = implode(', ', $validationErrors);
                return $result;
            }
            unset($data['id_pelanggan']); // Remove again for update

            // Check if name already exists (excluding current record)
            if (isset($data['nama']) && $this->pelangganModel->isNameExists($data['nama'], $id)) {
                $result['message'] = 'Nama pelanggan sudah terdaftar';
                return $result;
            }

            // Check if phone number already exists (excluding current record)
            if (isset($data['telepon']) && $this->pelangganModel->isPhoneExists($data['telepon'], $id)) {
                $result['message'] = 'Nomor telepon sudah terdaftar';
                return $result;
            }

            // Update customer
            if ($this->pelangganModel->savePelanggan($data, $id)) {
                $result['success'] = true;
                $result['message'] = 'Pelanggan berhasil diperbarui';
                $result['data'] = $this->pelangganModel->find($id);
            } else {
                $result['message'] = 'Gagal memperbarui pelanggan';
            }

        } catch (\Exception $e) {
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Delete customer
     */
    public function deleteCustomer(string $id): array
    {
        return $this->pelangganModel->deletePelanggan($id);
    }

    /**
     * Generate next customer ID
     */
    public function generateNextId(): string
    {
        return $this->pelangganModel->generateNextId();
    }

    /**
     * Get customers for dropdown/select options
     */
    public function getCustomersForSelect(): array
    {
        return $this->pelangganModel->getCustomersForSelect();
    }

    /**
     * Validate customer data
     */
    public function validateCustomerData(array $data, string $id = ''): array
    {
        $errors = [];

        // Validate required fields
        if (empty($data['nama'])) {
            $errors[] = 'Nama pelanggan harus diisi';
        }

        if (empty($data['telepon'])) {
            $errors[] = 'Nomor telepon harus diisi';
        }

        if (empty($data['alamat'])) {
            $errors[] = 'Alamat harus diisi';
        }

        // Validate ID format if provided
        if (!empty($data['id_pelanggan'])) {
            if (strlen($data['id_pelanggan']) > 7) {
                $errors[] = 'ID Pelanggan maksimal 7 karakter';
            }
            if (!preg_match('/^CST\d{4}$/', $data['id_pelanggan'])) {
                $errors[] = 'Format ID Pelanggan harus CSTxxxx (contoh: CST0001)';
            }
        }

        // Validate field lengths
        if (!empty($data['nama']) && strlen($data['nama']) > 30) {
            $errors[] = 'Nama pelanggan maksimal 30 karakter';
        }

        if (!empty($data['telepon']) && strlen($data['telepon']) > 15) {
            $errors[] = 'Nomor telepon maksimal 15 karakter';
        }

        if (!empty($data['alamat']) && strlen($data['alamat']) > 150) {
            $errors[] = 'Alamat maksimal 150 karakter';
        }

        // Validate phone number format
        if (!empty($data['telepon']) && !preg_match('/^[0-9+\-\s()]+$/', $data['telepon'])) {
            $errors[] = 'Format nomor telepon tidak valid';
        }

        // Validate phone number length
        if (!empty($data['telepon']) && strlen($data['telepon']) < 10) {
            $errors[] = 'Nomor telepon minimal 10 karakter';
        }

        return $errors;
    }

    /**
     * Check if customer can be deleted
     */
    public function canDelete(string $id): bool
    {
        return !$this->pelangganModel->isInUse($id);
    }

    /**
     * Search customers by keyword
     */
    public function searchCustomers(string $keyword, int $limit = 10): array
    {
        return $this->pelangganModel->searchCustomers($keyword, $limit);
    }

    /**
     * Get customer statistics
     */
    public function getCustomerStatistics(): array
    {
        return $this->pelangganModel->getCustomerStatistics();
    }

    /**
     * Get customer contact information
     */
    public function getCustomerContact(string $id): ?array
    {
        $customer = $this->getCustomerById($id);
        
        if (!$customer) {
            return null;
        }
        
        return $customer->getContactInfo();
    }

    /**
     * Validate phone number format
     */
    public function validatePhoneNumber(string $phone): bool
    {
        // Basic validation for Indonesian phone numbers
        return preg_match('/^[0-9+\-\s()]+$/', $phone) && strlen($phone) >= 10;
    }

    /**
     * Format phone number for display
     */
    public function formatPhoneNumber(string $phone): string
    {
        // Remove non-numeric characters except + and -
        $cleaned = preg_replace('/[^\d+\-]/', '', $phone);
        
        // Format Indonesian phone number
        if (strlen($cleaned) > 10) {
            return substr($cleaned, 0, 4) . '-' . substr($cleaned, 4, 4) . '-' . substr($cleaned, 8);
        }
        
        return $cleaned;
    }

    /**
     * Get customers by type
     */
    public function getCustomersByType(): array
    {
        $customers = $this->pelangganModel->findAll();
        $corporate = [];
        $individual = [];
        
        foreach ($customers as $customer) {
            if ($customer->getCustomerType() === 'Corporate') {
                $corporate[] = $customer;
            } else {
                $individual[] = $customer;
            }
        }
        
        return [
            'corporate' => $corporate,
            'individual' => $individual
        ];
    }
}