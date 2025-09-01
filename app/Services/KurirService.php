<?php

namespace App\Services;

use App\Models\KurirModel;
use App\Models\UserModel;
use App\Entities\KurirEntity;

class KurirService
{
    protected KurirModel $kurirModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->kurirModel = new KurirModel();
        $this->userModel = new UserModel();
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

            // Check if username already exists and make it unique if needed
            $originalUsername = $data['username'] ?? '';
            if (!empty($originalUsername)) {
                $username = $originalUsername;
                $counter = 1;
                while ($this->userModel->usernameExists($username)) {
                    $username = $originalUsername . '_' . $counter;
                    $counter++;
                    if ($counter > 100) { // Prevent infinite loop
                        $result['message'] = 'Gagal membuat username yang unik';
                        return $result;
                    }
                }
                $data['username'] = $username;
                
                if ($username !== $originalUsername) {
                    log_message('info', "KurirService: Username changed from '$originalUsername' to '$username' to avoid duplicates");
                }
            }

            // Generate user ID with more robust checking
            $userId = null;
            $maxRetries = 50;
            
            // Get fresh data from database to avoid caching issues
            $db = \Config\Database::connect();
            
            // Find the highest existing USR ID directly from database
            $query = $db->query("SELECT id_user FROM user WHERE id_user LIKE 'USR%' ORDER BY id_user DESC LIMIT 1");
            $lastUser = $query->getRow();
            
            if ($lastUser) {
                $lastNumber = (int) substr($lastUser->id_user, 3);
                $nextNumber = $lastNumber + 1;
                $userId = 'USR' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
            } else {
                $userId = 'USR01';
            }
            
            // Double-check that this ID doesn't exist (direct database check)
            $checkQuery = $db->query("SELECT COUNT(*) as count FROM user WHERE id_user = ?", [$userId]);
            $exists = $checkQuery->getRow()->count > 0;
            
            if ($exists) {
                // If it exists, try sequential IDs
                for ($i = 1; $i <= 99; $i++) {
                    $testId = 'USR' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $checkQuery = $db->query("SELECT COUNT(*) as count FROM user WHERE id_user = ?", [$testId]);
                    $testExists = $checkQuery->getRow()->count > 0;
                    
                    if (!$testExists) {
                        $userId = $testId;
                        break;
                    }
                }
            }
            
            // Final verification
            if (!$userId) {
                $result['message'] = 'Gagal generate ID user yang unik. Semua ID USR01-USR99 sudah terpakai.';
                return $result;
            }
            
            // Log the generated ID for debugging
            log_message('debug', "KurirService: Generated user ID: $userId");

            // Use transaction to ensure atomicity
            $db = \Config\Database::connect();
            $db->transStart();
            
            // Create user account for kurir first
            if (!empty($data['username']) && !empty($data['password'])) {
                $userData = [
                    'id_user' => $userId,
                    'username' => $data['username'],
                    'password' => password_hash($data['password'], PASSWORD_ARGON2ID),
                    'level' => 2 // Kurir level
                ];
                
                // Only add is_active if the field exists in the table
                $db = \Config\Database::connect();
                if ($db->fieldExists('is_active', 'user')) {
                    $userData['is_active'] = 1;
                }

                try {
                    // Debug: Log the data being inserted
                    log_message('debug', 'KurirService: Attempting to insert user data: ' . json_encode($userData));
                    
                    // Final check right before insertion
                    $finalCheck = $db->query("SELECT COUNT(*) as count FROM user WHERE id_user = ?", [$userId]);
                    $finalExists = $finalCheck->getRow()->count > 0;
                    
                    if ($finalExists) {
                        log_message('error', "KurirService: ID $userId exists at final check - this should not happen!");
                        $result['message'] = "ID user $userId sudah ada di database saat akan insert. Coba lagi.";
                        return $result;
                    }
                    
                    log_message('debug', "KurirService: Final check passed for ID: $userId");
                    
                    // First try using UserModel (preferred method for proper timestamp handling)
                    $this->userModel->skipValidation(true);
                    $userInsertResult = $this->userModel->insert($userData);
                    $this->userModel->skipValidation(false);
                    
                    // if ($userInsertResult) {
                    //     log_message('debug', 'KurirService: UserModel insert succeeded');
                    // } else {
                    //     // If UserModel fails, try direct database insert with manual timestamps
                    //     log_message('debug', 'KurirService: UserModel failed, trying direct insert with timestamps');
                        
                    //     $db = \Config\Database::connect();
                        
                    //     // Add timestamps manually
                    //     $userData['created_at'] = date('Y-m-d H:i:s');
                    //     $userData['updated_at'] = date('Y-m-d H:i:s');
                        
                    //     $builder = $db->table('user');
                    //     $directInsertResult = $builder->insert($userData);
                    //     $dbError = $db->error();
                    //     $lastQuery = $db->getLastQuery();
                        
                    //     log_message('debug', 'KurirService: Direct insert result: ' . ($directInsertResult ? 'SUCCESS' : 'FAILED'));
                    //     log_message('debug', 'KurirService: Last query: ' . $lastQuery);
                        
                    //     if (!$directInsertResult) {
                    //         $userErrors = $this->userModel->errors();
                    //         $errorMsg = 'Gagal membuat akun user untuk kurir';
                            
                    //         if (!empty($userErrors)) {
                    //             $errorMsg .= ' (Model errors): ' . implode(', ', $userErrors);
                    //         }
                            
                    //         if (!empty($dbError['message'])) {
                    //             $errorMsg .= ' (DB error): ' . $dbError['message'];
                    //         }
                            
                    //         $result['message'] = $errorMsg;
                    //         return $result;
                    //     }
                    // }
                    
                } catch (\Exception $userException) {
                    $db->transRollback();
                    log_message('error', 'KurirService: Exception during user creation: ' . $userException->getMessage());
                    $result['message'] = 'Gagal membuat akun user untuk kurir: ' . $userException->getMessage();
                    return $result;
                }
            }

            // Now create the kurir record
            $kurirData = $data;
            unset($kurirData['username']);
            $kurirData['password'] = ''; // Temporary empty password until migration is run

            // Save courier
            $saveResult = $this->kurirModel->saveKurir($kurirData);
            if (!$saveResult) {
                $db->transRollback();
                $errors = $this->kurirModel->errors();
                $result['message'] = 'Gagal menyimpan kurir: ' . (is_array($errors) ? implode(', ', $errors) : 'Unknown error');
                return $result;
            }

            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                $result['message'] = 'Gagal menyimpan data kurir dan user (transaction failed)';
                return $result;
            }

            $result['success'] = true;
            $result['message'] = 'Kurir berhasil dibuat';
            $result['data'] = $this->kurirModel->find($data['id_kurir']);

        } catch (\Exception $e) {
            // Rollback transaction if it's still active
            if (isset($db) && $db->transStatus() !== false) {
                $db->transRollback();
            }
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
            if (isset($data['telepon'])) {
                // Debug logging
                log_message('debug', "KurirService::updateCourier - Checking phone: {$data['telepon']} for courier ID: {$id}");
                
                // Only check if phone number has changed
                if ($data['telepon'] !== $existingCourier->telepon) {
                    if ($this->kurirModel->isPhoneExists($data['telepon'], $id)) {
                        log_message('debug', "KurirService::updateCourier - Phone {$data['telepon']} already exists for another courier");
                        $result['message'] = 'Nomor telepon sudah terdaftar';
                        return $result;
                    }
                } else {
                    log_message('debug', "KurirService::updateCourier - Phone number unchanged, skipping duplicate check");
                }
            }

            // Prepare kurir data (remove username and password - we don't update user table for edit)
            $kurirData = $data;
            unset($kurirData['username'], $kurirData['password']);

            // Update courier
            if ($this->kurirModel->saveKurir($kurirData, $id)) {
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

        // Validate username and password for new courier
        if (empty($id)) {
            if (empty($data['username'])) {
                $errors[] = 'Username harus diisi';
            }
            if (empty($data['password'])) {
                $errors[] = 'Password harus diisi';
            }
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

        // Validate username and password length
        if (!empty($data['username']) && strlen($data['username']) < 3) {
            $errors[] = 'Username minimal 3 karakter';
        }
        
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