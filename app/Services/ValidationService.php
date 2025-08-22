<?php

namespace App\Services;

use CodeIgniter\Validation\Validation;
use Config\Services;

class ValidationService
{
    protected Validation $validation;
    
    public function __construct()
    {
        $this->validation = Services::validation();
    }

    /**
     * Validate user data
     */
    public function validateUserData(array $data, bool $isUpdate = false): array
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|alpha_numeric',
            'level' => 'required|valid_user_level',
        ];

        if (!$isUpdate) {
            $rules['username'] .= '|is_unique[user.username]';
            $rules['password'] = 'required|strong_password';
        } else {
            if (isset($data['password']) && !empty($data['password'])) {
                $rules['password'] = 'strong_password';
            }
        }

        $messages = [
            'username' => [
                'required' => 'Username is required',
                'min_length' => 'Username must be at least 3 characters',
                'max_length' => 'Username cannot exceed 50 characters',
                'alpha_numeric' => 'Username can only contain letters and numbers',
                'is_unique' => 'Username already exists'
            ],
            'password' => [
                'required' => 'Password is required',
                'strong_password' => 'Password must be at least 8 characters with uppercase, lowercase, and number'
            ],
            'level' => [
                'required' => 'User level is required',
                'valid_user_level' => 'Invalid user level'
            ]
        ];

        return $this->runValidation($data, $rules, $messages);
    }

    /**
     * Validate shipment data
     */
    public function validateShipmentData(array $data, bool $isUpdate = false): array
    {
        $rules = [
            'id_pengiriman' => 'required|valid_shipment_id',
            'tanggal' => 'required|valid_business_date',
            'id_pelanggan' => 'required|customer_exists',
            'id_kurir' => 'required|courier_exists',
            'no_kendaraan' => 'permit_empty|valid_vehicle_number',
            'no_po' => 'permit_empty|max_length[50]',
            'keterangan' => 'permit_empty|max_length[500]',
            'penerima' => 'permit_empty|valid_indonesian_name|max_length[100]',
        ];

        if (!$isUpdate) {
            $rules['id_pengiriman'] .= '|unique_shipment_id';
        }

        $messages = [
            'id_pengiriman' => [
                'required' => 'Shipment ID is required',
                'valid_shipment_id' => 'Invalid shipment ID format (should be KRM + date + sequence)',
                'unique_shipment_id' => 'Shipment ID already exists'
            ],
            'tanggal' => [
                'required' => 'Date is required',
                'valid_business_date' => 'Invalid date or date is too far in the future'
            ],
            'id_pelanggan' => [
                'required' => 'Customer is required',
                'customer_exists' => 'Selected customer does not exist'
            ],
            'id_kurir' => [
                'required' => 'Courier is required',
                'courier_exists' => 'Selected courier does not exist'
            ],
            'no_kendaraan' => [
                'valid_vehicle_number' => 'Invalid vehicle number format'
            ],
            'no_po' => [
                'max_length' => 'PO number cannot exceed 50 characters'
            ],
            'keterangan' => [
                'max_length' => 'Description cannot exceed 500 characters'
            ],
            'penerima' => [
                'valid_indonesian_name' => 'Invalid recipient name format',
                'max_length' => 'Recipient name cannot exceed 100 characters'
            ]
        ];

        return $this->runValidation($data, $rules, $messages);
    }

    /**
     * Validate shipment detail data
     */
    public function validateShipmentDetailData(array $data): array
    {
        $rules = [
            'id_barang' => 'required|is_not_unique[barang.id_barang]',
            'qty' => 'required|valid_quantity',
            'berat' => 'permit_empty|valid_weight',
            'dimensi' => 'permit_empty|valid_dimensions',
            'keterangan' => 'permit_empty|max_length[255]',
        ];

        $messages = [
            'id_barang' => [
                'required' => 'Item is required',
                'is_not_unique' => 'Selected item does not exist'
            ],
            'qty' => [
                'required' => 'Quantity is required',
                'valid_quantity' => 'Invalid quantity (must be positive integer)'
            ],
            'berat' => [
                'valid_weight' => 'Invalid weight (must be positive number, max 10000 kg)'
            ],
            'dimensi' => [
                'valid_dimensions' => 'Invalid dimensions format (use: length x width x height)'
            ],
            'keterangan' => [
                'max_length' => 'Description cannot exceed 255 characters'
            ]
        ];

        return $this->runValidation($data, $rules, $messages);
    }

    /**
     * Validate customer data
     */
    public function validateCustomerData(array $data, bool $isUpdate = false): array
    {
        $rules = [
            'nama_pelanggan' => 'required|valid_company_name|max_length[100]',
            'alamat' => 'required|valid_address|max_length[255]',
            'telepon' => 'permit_empty|valid_phone_number',
            'email' => 'permit_empty|valid_email|max_length[100]',
            'kode_pos' => 'permit_empty|valid_postal_code',
        ];

        if (!$isUpdate) {
            $rules['id_pelanggan'] = 'required|is_unique[pelanggan.id_pelanggan]';
        }

        $messages = [
            'id_pelanggan' => [
                'required' => 'Customer ID is required',
                'is_unique' => 'Customer ID already exists'
            ],
            'nama_pelanggan' => [
                'required' => 'Customer name is required',
                'valid_company_name' => 'Invalid customer name format',
                'max_length' => 'Customer name cannot exceed 100 characters'
            ],
            'alamat' => [
                'required' => 'Address is required',
                'valid_address' => 'Invalid address format',
                'max_length' => 'Address cannot exceed 255 characters'
            ],
            'telepon' => [
                'valid_phone_number' => 'Invalid phone number format'
            ],
            'email' => [
                'valid_email' => 'Invalid email format',
                'max_length' => 'Email cannot exceed 100 characters'
            ],
            'kode_pos' => [
                'valid_postal_code' => 'Invalid postal code (must be 5 digits)'
            ]
        ];

        return $this->runValidation($data, $rules, $messages);
    }

    /**
     * Validate courier data
     */
    public function validateCourierData(array $data, bool $isUpdate = false): array
    {
        $rules = [
            'nama_kurir' => 'required|valid_indonesian_name|max_length[100]',
            'telepon' => 'required|valid_phone_number',
            'alamat' => 'permit_empty|valid_address|max_length[255]',
        ];

        if (!$isUpdate) {
            $rules['id_kurir'] = 'required|is_unique[kurir.id_kurir]';
        }

        $messages = [
            'id_kurir' => [
                'required' => 'Courier ID is required',
                'is_unique' => 'Courier ID already exists'
            ],
            'nama_kurir' => [
                'required' => 'Courier name is required',
                'valid_indonesian_name' => 'Invalid courier name format',
                'max_length' => 'Courier name cannot exceed 100 characters'
            ],
            'telepon' => [
                'required' => 'Phone number is required',
                'valid_phone_number' => 'Invalid phone number format'
            ],
            'alamat' => [
                'valid_address' => 'Invalid address format',
                'max_length' => 'Address cannot exceed 255 characters'
            ]
        ];

        return $this->runValidation($data, $rules, $messages);
    }

    /**
     * Validate item data
     */
    public function validateItemData(array $data, bool $isUpdate = false): array
    {
        $rules = [
            'nama_barang' => 'required|max_length[100]',
            'id_kategori' => 'required|category_exists',
            'satuan' => 'permit_empty|max_length[20]',
            'keterangan' => 'permit_empty|max_length[255]',
        ];

        if (!$isUpdate) {
            $rules['id_barang'] = 'required|is_unique[barang.id_barang]';
        }

        $messages = [
            'id_barang' => [
                'required' => 'Item ID is required',
                'is_unique' => 'Item ID already exists'
            ],
            'nama_barang' => [
                'required' => 'Item name is required',
                'max_length' => 'Item name cannot exceed 100 characters'
            ],
            'id_kategori' => [
                'required' => 'Category is required',
                'category_exists' => 'Selected category does not exist'
            ],
            'satuan' => [
                'max_length' => 'Unit cannot exceed 20 characters'
            ],
            'keterangan' => [
                'max_length' => 'Description cannot exceed 255 characters'
            ]
        ];

        return $this->runValidation($data, $rules, $messages);
    }

    /**
     * Validate category data
     */
    public function validateCategoryData(array $data, bool $isUpdate = false): array
    {
        $rules = [
            'nama_kategori' => 'required|max_length[50]',
            'keterangan' => 'permit_empty|max_length[255]',
        ];

        if (!$isUpdate) {
            $rules['id_kategori'] = 'required|is_unique[kategori.id_kategori]';
        }

        $messages = [
            'id_kategori' => [
                'required' => 'Category ID is required',
                'is_unique' => 'Category ID already exists'
            ],
            'nama_kategori' => [
                'required' => 'Category name is required',
                'max_length' => 'Category name cannot exceed 50 characters'
            ],
            'keterangan' => [
                'max_length' => 'Description cannot exceed 255 characters'
            ]
        ];

        return $this->runValidation($data, $rules, $messages);
    }

    /**
     * Validate file upload
     */
    public function validateFileUpload(array $fileData, string $type = 'image'): array
    {
        $rules = [];
        $messages = [];

        if ($type === 'image') {
            $rules = [
                'uploaded[file]',
                'max_size[file,5120]', // 5MB
                'ext_in[file,jpg,jpeg,png,gif,webp]',
                'mime_in[file,image/jpg,image/jpeg,image/png,image/gif,image/webp]'
            ];
            
            $messages = [
                'file' => [
                    'uploaded' => 'Please select a file to upload',
                    'max_size' => 'File size cannot exceed 5MB',
                    'ext_in' => 'Only JPG, JPEG, PNG, GIF, and WebP files are allowed',
                    'mime_in' => 'Invalid file type'
                ]
            ];
        }

        return $this->runValidation($fileData, ['file' => implode('|', $rules)], $messages);
    }

    /**
     * Run validation and return results
     */
    private function runValidation(array $data, array $rules, array $messages = []): array
    {
        $this->validation->setRules($rules, $messages);
        
        if ($this->validation->run($data)) {
            return ['valid' => true, 'errors' => []];
        }
        
        return ['valid' => false, 'errors' => $this->validation->getErrors()];
    }

    /**
     * Get validation instance for custom validation
     */
    public function getValidation(): Validation
    {
        return $this->validation;
    }

    /**
     * Validate array of data (for bulk operations)
     */
    public function validateBulkData(array $dataArray, callable $validationCallback): array
    {
        $results = [];
        $hasErrors = false;

        foreach ($dataArray as $index => $data) {
            $result = $validationCallback($data);
            $results[$index] = $result;
            
            if (!$result['valid']) {
                $hasErrors = true;
            }
        }

        return [
            'valid' => !$hasErrors,
            'results' => $results
        ];
    }
}