<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        \App\Validation\CustomRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    /**
     * User validation rules for registration/update
     */
    public array $user = [
        'username' => 'required|min_length[3]|max_length[50]|alpha_numeric',
        'password' => 'required|strong_password',
        'level' => 'required|valid_user_level',
    ];

    /**
     * User validation rules for login
     */
    public array $login = [
        'username' => 'required|min_length[3]|max_length[50]',
        'password' => 'required|min_length[1]',
    ];

    /**
     * Shipment validation rules
     */
    public array $shipment = [
        'id_pengiriman' => 'required|valid_shipment_id|unique_shipment_id',
        'tanggal' => 'required|valid_business_date',
        'id_pelanggan' => 'required|customer_exists',
        'id_kurir' => 'required|courier_exists',
        'no_kendaraan' => 'permit_empty|valid_vehicle_number',
        'no_po' => 'permit_empty|max_length[50]',
        'keterangan' => 'permit_empty|max_length[500]',
        'penerima' => 'permit_empty|valid_indonesian_name|max_length[100]',
    ];

    /**
     * Shipment detail validation rules
     */
    public array $shipment_detail = [
        'id_barang' => 'required|is_not_unique[barang.id_barang]',
        'qty' => 'required|valid_quantity',
        'berat' => 'permit_empty|valid_weight',
        'dimensi' => 'permit_empty|valid_dimensions',
        'keterangan' => 'permit_empty|max_length[255]',
    ];

    /**
     * Customer validation rules
     */
    public array $customer = [
        'id_pelanggan' => 'required|is_unique[pelanggan.id_pelanggan]',
        'nama_pelanggan' => 'required|valid_company_name|max_length[100]',
        'alamat' => 'required|valid_address|max_length[255]',
        'telepon' => 'permit_empty|valid_phone_number',
        'email' => 'permit_empty|valid_email|max_length[100]',
        'kode_pos' => 'permit_empty|valid_postal_code',
    ];

    /**
     * Courier validation rules
     */
    public array $courier = [
        'id_kurir' => 'required|is_unique[kurir.id_kurir]',
        'nama_kurir' => 'required|valid_indonesian_name|max_length[100]',
        'telepon' => 'required|valid_phone_number',
        'alamat' => 'permit_empty|valid_address|max_length[255]',
    ];

    /**
     * Item validation rules
     */
    public array $item = [
        'id_barang' => 'required|is_unique[barang.id_barang]',
        'nama_barang' => 'required|max_length[100]',
        'id_kategori' => 'required|category_exists',
        'satuan' => 'permit_empty|max_length[20]',
        'keterangan' => 'permit_empty|max_length[255]',
    ];

    /**
     * Category validation rules
     */
    public array $category = [
        'id_kategori' => 'required|is_unique[kategori.id_kategori]',
        'nama_kategori' => 'required|max_length[50]',
        'keterangan' => 'permit_empty|max_length[255]',
    ];

    /**
     * File upload validation rules
     */
    public array $file_upload = [
        'file' => 'uploaded[file]|max_size[file,5120]|ext_in[file,jpg,jpeg,png,gif,webp]',
    ];

    // --------------------------------------------------------------------
    // Error Messages
    // --------------------------------------------------------------------

    /**
     * User validation error messages
     */
    public array $user_errors = [
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

    /**
     * Login validation error messages
     */
    public array $login_errors = [
        'username' => [
            'required' => 'Username is required',
            'min_length' => 'Username is too short',
            'max_length' => 'Username is too long'
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password is required'
        ]
    ];

    /**
     * Shipment validation error messages
     */
    public array $shipment_errors = [
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

    /**
     * File upload error messages
     */
    public array $file_upload_errors = [
        'file' => [
            'uploaded' => 'Please select a file to upload',
            'max_size' => 'File size cannot exceed 5MB',
            'ext_in' => 'Only JPG, JPEG, PNG, GIF, and WebP files are allowed'
        ]
    ];
}