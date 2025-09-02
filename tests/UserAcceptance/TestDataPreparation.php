<?php

namespace Tests\UserAcceptance;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Test Data Preparation for User Acceptance Testing
 * 
 * This class prepares comprehensive test data for UAT scenarios
 * including users, categories, items, customers, couriers, and shipments.
 */
class TestDataPreparation extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;

    /**
     * Prepare all test data for UAT
     */
    public function prepareAllTestData(): void
    {
        echo "Preparing comprehensive test data for User Acceptance Testing...\n\n";

        $this->prepareUserAccounts();
        $this->prepareCategories();
        $this->prepareItems();
        $this->prepareCustomers();
        $this->prepareCouriers();
        $this->prepareShipments();
        $this->prepareHistoricalData();

        echo "\n✅ All test data prepared successfully!\n";
        $this->generateTestDataReport();
    }

    /**
     * Prepare user accounts for testing
     */
    private function prepareUserAccounts(): void
    {
        echo "Creating user accounts...\n";

        $userModel = new \App\Models\UserModel();

        $users = [
            [
                'id_user' => 'ADM01',
                'username' => 'adminpuninar',
                'password' => password_hash('AdminPuninar123', PASSWORD_DEFAULT),
                'level' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_user' => 'FIN01',
                'username' => 'kurirpuninar',
                'password' => password_hash('KurirPuninar123', PASSWORD_DEFAULT),
                'level' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_user' => 'GDG01',
                'username' => 'gudangpuninar',
                'password' => password_hash('GudangPuninar123', PASSWORD_DEFAULT),
                'level' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_user' => 'TST01',
                'username' => 'testuser01',
                'password' => password_hash('TestUser123', PASSWORD_DEFAULT),
                'level' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($users as $user) {
            $userModel->insert($user);
        }

        echo "  ✓ Created " . count($users) . " user accounts\n";
    }

    /**
     * Prepare item categories
     */
    private function prepareCategories(): void
    {
        echo "Creating item categories...\n";

        $kategoriModel = new \App\Models\KategoriModel();

        $categories = [
            ['id_kategori' => 'KAT001', 'nama_kategori' => 'Electronics'],
            ['id_kategori' => 'KAT002', 'nama_kategori' => 'Furniture'],
            ['id_kategori' => 'KAT003', 'nama_kategori' => 'Clothing'],
            ['id_kategori' => 'KAT004', 'nama_kategori' => 'Books'],
            ['id_kategori' => 'KAT005', 'nama_kategori' => 'Automotive'],
            ['id_kategori' => 'KAT006', 'nama_kategori' => 'Sports Equipment'],
            ['id_kategori' => 'KAT007', 'nama_kategori' => 'Home Appliances'],
            ['id_kategori' => 'KAT008', 'nama_kategori' => 'Office Supplies'],
            ['id_kategori' => 'KAT009', 'nama_kategori' => 'Medical Equipment'],
            ['id_kategori' => 'KAT010', 'nama_kategori' => 'Industrial Tools']
        ];

        foreach ($categories as $category) {
            $kategoriModel->insert($category);
        }

        echo "  ✓ Created " . count($categories) . " categories\n";
    }

    /**
     * Prepare inventory items
     */
    private function prepareItems(): void
    {
        echo "Creating inventory items...\n";

        $barangModel = new \App\Models\BarangModel();

        $items = [
            // Electronics
            ['id_barang' => 'BRG001', 'nama_barang' => 'Laptop Dell Inspiron', 'id_kategori' => 'KAT001', 'satuan' => 'Unit', 'harga' => 15000000],
            ['id_barang' => 'BRG002', 'nama_barang' => 'Smartphone Samsung Galaxy', 'id_kategori' => 'KAT001', 'satuan' => 'Unit', 'harga' => 8000000],
            ['id_barang' => 'BRG003', 'nama_barang' => 'Tablet iPad Pro', 'id_kategori' => 'KAT001', 'satuan' => 'Unit', 'harga' => 12000000],
            ['id_barang' => 'BRG004', 'nama_barang' => 'Wireless Headphones', 'id_kategori' => 'KAT001', 'satuan' => 'Unit', 'harga' => 2500000],
            ['id_barang' => 'BRG005', 'nama_barang' => 'Smart TV 55 inch', 'id_kategori' => 'KAT001', 'satuan' => 'Unit', 'harga' => 18000000],

            // Furniture
            ['id_barang' => 'BRG006', 'nama_barang' => 'Office Chair Executive', 'id_kategori' => 'KAT002', 'satuan' => 'Unit', 'harga' => 3500000],
            ['id_barang' => 'BRG007', 'nama_barang' => 'Wooden Desk L-Shape', 'id_kategori' => 'KAT002', 'satuan' => 'Unit', 'harga' => 5000000],
            ['id_barang' => 'BRG008', 'nama_barang' => 'Bookshelf 5 Tier', 'id_kategori' => 'KAT002', 'satuan' => 'Unit', 'harga' => 2000000],
            ['id_barang' => 'BRG009', 'nama_barang' => 'Conference Table', 'id_kategori' => 'KAT002', 'satuan' => 'Unit', 'harga' => 8000000],
            ['id_barang' => 'BRG010', 'nama_barang' => 'Filing Cabinet 4 Drawer', 'id_kategori' => 'KAT002', 'satuan' => 'Unit', 'harga' => 2500000],

            // Clothing
            ['id_barang' => 'BRG011', 'nama_barang' => 'Business Suit Navy', 'id_kategori' => 'KAT003', 'satuan' => 'Set', 'harga' => 2500000],
            ['id_barang' => 'BRG012', 'nama_barang' => 'Formal Shirt White', 'id_kategori' => 'KAT003', 'satuan' => 'Piece', 'harga' => 500000],
            ['id_barang' => 'BRG013', 'nama_barang' => 'Leather Shoes Black', 'id_kategori' => 'KAT003', 'satuan' => 'Pair', 'harga' => 1500000],
            ['id_barang' => 'BRG014', 'nama_barang' => 'Silk Tie Collection', 'id_kategori' => 'KAT003', 'satuan' => 'Set', 'harga' => 750000],
            ['id_barang' => 'BRG015', 'nama_barang' => 'Winter Jacket', 'id_kategori' => 'KAT003', 'satuan' => 'Piece', 'harga' => 1200000],

            // Books
            ['id_barang' => 'BRG016', 'nama_barang' => 'Programming Textbook Set', 'id_kategori' => 'KAT004', 'satuan' => 'Set', 'harga' => 1500000],
            ['id_barang' => 'BRG017', 'nama_barang' => 'Business Management Guide', 'id_kategori' => 'KAT004', 'satuan' => 'Book', 'harga' => 300000],
            ['id_barang' => 'BRG018', 'nama_barang' => 'Technical Manual Collection', 'id_kategori' => 'KAT004', 'satuan' => 'Set', 'harga' => 2000000],

            // Automotive
            ['id_barang' => 'BRG019', 'nama_barang' => 'Car Battery 12V', 'id_kategori' => 'KAT005', 'satuan' => 'Unit', 'harga' => 1200000],
            ['id_barang' => 'BRG020', 'nama_barang' => 'Tire Set 4 pieces', 'id_kategori' => 'KAT005', 'satuan' => 'Set', 'harga' => 4000000],

            // Sports Equipment
            ['id_barang' => 'BRG021', 'nama_barang' => 'Treadmill Professional', 'id_kategori' => 'KAT006', 'satuan' => 'Unit', 'harga' => 25000000],
            ['id_barang' => 'BRG022', 'nama_barang' => 'Dumbell Set Complete', 'id_kategori' => 'KAT006', 'satuan' => 'Set', 'harga' => 5000000],

            // Home Appliances
            ['id_barang' => 'BRG023', 'nama_barang' => 'Refrigerator 2 Door', 'id_kategori' => 'KAT007', 'satuan' => 'Unit', 'harga' => 8000000],
            ['id_barang' => 'BRG024', 'nama_barang' => 'Washing Machine Front Load', 'id_kategori' => 'KAT007', 'satuan' => 'Unit', 'harga' => 6000000],
            ['id_barang' => 'BRG025', 'nama_barang' => 'Air Conditioner 1.5 PK', 'id_kategori' => 'KAT007', 'satuan' => 'Unit', 'harga' => 4500000],

            // Office Supplies
            ['id_barang' => 'BRG026', 'nama_barang' => 'Printer Laser A4', 'id_kategori' => 'KAT008', 'satuan' => 'Unit', 'harga' => 3500000],
            ['id_barang' => 'BRG027', 'nama_barang' => 'Paper A4 Box 5 Ream', 'id_kategori' => 'KAT008', 'satuan' => 'Box', 'harga' => 300000],
            ['id_barang' => 'BRG028', 'nama_barang' => 'Stationery Set Complete', 'id_kategori' => 'KAT008', 'satuan' => 'Set', 'harga' => 500000],

            // Medical Equipment
            ['id_barang' => 'BRG029', 'nama_barang' => 'Blood Pressure Monitor', 'id_kategori' => 'KAT009', 'satuan' => 'Unit', 'harga' => 2000000],
            ['id_barang' => 'BRG030', 'nama_barang' => 'Thermometer Digital', 'id_kategori' => 'KAT009', 'satuan' => 'Unit', 'harga' => 500000],

            // Industrial Tools
            ['id_barang' => 'BRG031', 'nama_barang' => 'Power Drill Set', 'id_kategori' => 'KAT010', 'satuan' => 'Set', 'harga' => 2500000],
            ['id_barang' => 'BRG032', 'nama_barang' => 'Welding Machine', 'id_kategori' => 'KAT010', 'satuan' => 'Unit', 'harga' => 15000000],
            ['id_barang' => 'BRG033', 'nama_barang' => 'Tool Box Professional', 'id_kategori' => 'KAT010', 'satuan' => 'Set', 'harga' => 3000000],

            // Additional items for testing
            ['id_barang' => 'BRG034', 'nama_barang' => 'Test Item Alpha', 'id_kategori' => 'KAT001', 'satuan' => 'Unit', 'harga' => 1000000],
            ['id_barang' => 'BRG035', 'nama_barang' => 'Test Item Beta', 'id_kategori' => 'KAT002', 'satuan' => 'Unit', 'harga' => 1500000],
            ['id_barang' => 'BRG036', 'nama_barang' => 'Test Item Gamma', 'id_kategori' => 'KAT003', 'satuan' => 'Unit', 'harga' => 2000000],
            ['id_barang' => 'BRG037', 'nama_barang' => 'Test Item Delta', 'id_kategori' => 'KAT004', 'satuan' => 'Unit', 'harga' => 2500000],
            ['id_barang' => 'BRG038', 'nama_barang' => 'Test Item Epsilon', 'id_kategori' => 'KAT005', 'satuan' => 'Unit', 'harga' => 3000000],
            ['id_barang' => 'BRG039', 'nama_barang' => 'Test Item Zeta', 'id_kategori' => 'KAT006', 'satuan' => 'Unit', 'harga' => 3500000],
            ['id_barang' => 'BRG040', 'nama_barang' => 'Test Item Eta', 'id_kategori' => 'KAT007', 'satuan' => 'Unit', 'harga' => 4000000],
            ['id_barang' => 'BRG041', 'nama_barang' => 'Test Item Theta', 'id_kategori' => 'KAT008', 'satuan' => 'Unit', 'harga' => 4500000],
            ['id_barang' => 'BRG042', 'nama_barang' => 'Test Item Iota', 'id_kategori' => 'KAT009', 'satuan' => 'Unit', 'harga' => 5000000],
            ['id_barang' => 'BRG043', 'nama_barang' => 'Test Item Kappa', 'id_kategori' => 'KAT010', 'satuan' => 'Unit', 'harga' => 5500000],
            ['id_barang' => 'BRG044', 'nama_barang' => 'Test Item Lambda', 'id_kategori' => 'KAT001', 'satuan' => 'Unit', 'harga' => 6000000],
            ['id_barang' => 'BRG045', 'nama_barang' => 'Test Item Mu', 'id_kategori' => 'KAT002', 'satuan' => 'Unit', 'harga' => 6500000],
            ['id_barang' => 'BRG046', 'nama_barang' => 'Test Item Nu', 'id_kategori' => 'KAT003', 'satuan' => 'Unit', 'harga' => 7000000],
            ['id_barang' => 'BRG047', 'nama_barang' => 'Test Item Xi', 'id_kategori' => 'KAT004', 'satuan' => 'Unit', 'harga' => 7500000],
            ['id_barang' => 'BRG048', 'nama_barang' => 'Test Item Omicron', 'id_kategori' => 'KAT005', 'satuan' => 'Unit', 'harga' => 8000000],
            ['id_barang' => 'BRG049', 'nama_barang' => 'Test Item Pi', 'id_kategori' => 'KAT006', 'satuan' => 'Unit', 'harga' => 8500000],
            ['id_barang' => 'BRG050', 'nama_barang' => 'Test Item Rho', 'id_kategori' => 'KAT007', 'satuan' => 'Unit', 'harga' => 9000000]
        ];

        foreach ($items as $item) {
            $barangModel->insert($item);
        }

        echo "  ✓ Created " . count($items) . " inventory items\n";
    }

    /**
     * Prepare customer data
     */
    private function prepareCustomers(): void
    {
        echo "Creating customer accounts...\n";

        $pelangganModel = new \App\Models\PelangganModel();

        $customers = [
            ['id_pelanggan' => 'PLG001', 'nama_pelanggan' => 'PT. Teknologi Maju', 'alamat' => 'Jl. Sudirman No. 123, Jakarta', 'telepon' => '021-12345678'],
            ['id_pelanggan' => 'PLG002', 'nama_pelanggan' => 'CV. Berkah Jaya', 'alamat' => 'Jl. Gatot Subroto No. 456, Bandung', 'telepon' => '022-87654321'],
            ['id_pelanggan' => 'PLG003', 'nama_pelanggan' => 'PT. Sinar Harapan', 'alamat' => 'Jl. Ahmad Yani No. 789, Surabaya', 'telepon' => '031-11223344'],
            ['id_pelanggan' => 'PLG004', 'nama_pelanggan' => 'UD. Maju Bersama', 'alamat' => 'Jl. Diponegoro No. 321, Yogyakarta', 'telepon' => '0274-55667788'],
            ['id_pelanggan' => 'PLG005', 'nama_pelanggan' => 'PT. Global Solutions', 'alamat' => 'Jl. HR Rasuna Said No. 654, Jakarta', 'telepon' => '021-99887766'],
            ['id_pelanggan' => 'PLG006', 'nama_pelanggan' => 'CV. Karya Mandiri', 'alamat' => 'Jl. Pahlawan No. 987, Medan', 'telepon' => '061-44556677'],
            ['id_pelanggan' => 'PLG007', 'nama_pelanggan' => 'PT. Nusantara Prima', 'alamat' => 'Jl. Veteran No. 147, Semarang', 'telepon' => '024-22334455'],
            ['id_pelanggan' => 'PLG008', 'nama_pelanggan' => 'UD. Sejahtera Abadi', 'alamat' => 'Jl. Merdeka No. 258, Malang', 'telepon' => '0341-66778899'],
            ['id_pelanggan' => 'PLG009', 'nama_pelanggan' => 'PT. Inovasi Digital', 'alamat' => 'Jl. Kemerdekaan No. 369, Denpasar', 'telepon' => '0361-33445566'],
            ['id_pelanggan' => 'PLG010', 'nama_pelanggan' => 'CV. Sukses Makmur', 'alamat' => 'Jl. Proklamasi No. 741, Makassar', 'telepon' => '0411-77889900'],
            ['id_pelanggan' => 'PLG011', 'nama_pelanggan' => 'PT. Mitra Sejati', 'alamat' => 'Jl. Pancasila No. 852, Palembang', 'telepon' => '0711-11223344'],
            ['id_pelanggan' => 'PLG012', 'nama_pelanggan' => 'UD. Harapan Baru', 'alamat' => 'Jl. Garuda No. 963, Balikpapan', 'telepon' => '0542-55667788'],
            ['id_pelanggan' => 'PLG013', 'nama_pelanggan' => 'PT. Cemerlang Jaya', 'alamat' => 'Jl. Bhayangkara No. 159, Pontianak', 'telepon' => '0561-99001122'],
            ['id_pelanggan' => 'PLG014', 'nama_pelanggan' => 'CV. Dinamis Group', 'alamat' => 'Jl. Kartini No. 357, Banjarmasin', 'telepon' => '0511-33445566'],
            ['id_pelanggan' => 'PLG015', 'nama_pelanggan' => 'PT. Visioner Tech', 'alamat' => 'Jl. Cut Nyak Dien No. 468, Banda Aceh', 'telepon' => '0651-77889900'],
            ['id_pelanggan' => 'PLG016', 'nama_pelanggan' => 'UD. Berkembang Pesat', 'alamat' => 'Jl. Teuku Umar No. 579, Pekanbaru', 'telepon' => '0761-11223344'],
            ['id_pelanggan' => 'PLG017', 'nama_pelanggan' => 'PT. Solusi Terpadu', 'alamat' => 'Jl. Sultan Hasanuddin No. 681, Jambi', 'telepon' => '0741-55667788'],
            ['id_pelanggan' => 'PLG018', 'nama_pelanggan' => 'CV. Prestasi Tinggi', 'alamat' => 'Jl. Hang Tuah No. 792, Batam', 'telepon' => '0778-99001122'],
            ['id_pelanggan' => 'PLG019', 'nama_pelanggan' => 'PT. Unggul Kompetitif', 'alamat' => 'Jl. Soekarno Hatta No. 813, Lampung', 'telepon' => '0721-33445566'],
            ['id_pelanggan' => 'PLG020', 'nama_pelanggan' => 'UD. Mandiri Sukses', 'alamat' => 'Jl. Jenderal Sudirman No. 924, Manado', 'telepon' => '0431-77889900']
        ];

        foreach ($customers as $customer) {
            $pelangganModel->insert($customer);
        }

        echo "  ✓ Created " . count($customers) . " customer accounts\n";
    }

    /**
     * Prepare courier data
     */
    private function prepareCouriers(): void
    {
        echo "Creating courier accounts...\n";

        $kurirModel = new \App\Models\KurirModel();

        $couriers = [
            ['id_kurir' => 'KUR001', 'nama_kurir' => 'Ahmad Kurniawan', 'alamat' => 'Jl. Mawar No. 12, Jakarta', 'telepon' => '081234567890'],
            ['id_kurir' => 'KUR002', 'nama_kurir' => 'Budi Santoso', 'alamat' => 'Jl. Melati No. 34, Bandung', 'telepon' => '082345678901'],
            ['id_kurir' => 'KUR003', 'nama_kurir' => 'Citra Dewi', 'alamat' => 'Jl. Anggrek No. 56, Surabaya', 'telepon' => '083456789012'],
            ['id_kurir' => 'KUR004', 'nama_kurir' => 'Dedi Prasetyo', 'alamat' => 'Jl. Kenanga No. 78, Yogyakarta', 'telepon' => '084567890123'],
            ['id_kurir' => 'KUR005', 'nama_kurir' => 'Eka Sari', 'alamat' => 'Jl. Dahlia No. 90, Medan', 'telepon' => '085678901234'],
            ['id_kurir' => 'KUR006', 'nama_kurir' => 'Fajar Hidayat', 'alamat' => 'Jl. Tulip No. 11, Semarang', 'telepon' => '086789012345'],
            ['id_kurir' => 'KUR007', 'nama_kurir' => 'Gita Permata', 'alamat' => 'Jl. Sakura No. 22, Malang', 'telepon' => '087890123456'],
            ['id_kurir' => 'KUR008', 'nama_kurir' => 'Hendra Wijaya', 'alamat' => 'Jl. Flamboyan No. 33, Denpasar', 'telepon' => '088901234567'],
            ['id_kurir' => 'KUR009', 'nama_kurir' => 'Indah Lestari', 'alamat' => 'Jl. Bougenville No. 44, Makassar', 'telepon' => '089012345678'],
            ['id_kurir' => 'KUR010', 'nama_kurir' => 'Joko Susilo', 'alamat' => 'Jl. Kamboja No. 55, Palembang', 'telepon' => '081123456789']
        ];

        foreach ($couriers as $courier) {
            $kurirModel->insert($courier);
        }

        echo "  ✓ Created " . count($couriers) . " courier accounts\n";
    }

    /**
     * Prepare shipment data
     */
    private function prepareShipments(): void
    {
        echo "Creating shipment records...\n";

        $pengirimanModel = new \App\Models\PengirimanModel();
        $detailModel = new \App\Models\DetailPengirimanModel();

        // Create shipments for the last 30 days
        $shipments = [];
        $details = [];

        for ($i = 1; $i <= 30; $i++) {
            $shipmentId = 'SHP' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $customerId = 'PLG' . str_pad(rand(1, 20), 3, '0', STR_PAD_LEFT);
            $courierId = 'KUR' . str_pad(rand(1, 10), 3, '0', STR_PAD_LEFT);
            $status = rand(1, 4); // 1=Pending, 2=In Transit, 3=Delivered, 4=Cancelled

            $shipments[] = [
                'id_pengiriman' => $shipmentId,
                'tanggal' => $date,
                'id_pelanggan' => $customerId,
                'id_kurir' => $courierId,
                'no_kendaraan' => 'B ' . rand(1000, 9999) . ' ABC',
                'no_po' => 'PO' . date('Ymd') . str_pad($i, 3, '0', STR_PAD_LEFT),
                'keterangan' => 'Shipment for testing purposes - ' . $shipmentId,
                'penerima' => 'Receiver ' . $i,
                'photo' => null,
                'status' => $status
            ];

            // Add 1-5 items per shipment
            $itemCount = rand(1, 5);
            for ($j = 1; $j <= $itemCount; $j++) {
                $itemId = 'BRG' . str_pad(rand(1, 50), 3, '0', STR_PAD_LEFT);
                $quantity = rand(1, 10);

                $details[] = [
                    'id_pengiriman' => $shipmentId,
                    'id_barang' => $itemId,
                    'jumlah' => $quantity,
                    'keterangan' => 'Item detail for ' . $shipmentId . ' - Item ' . $j
                ];
            }
        }

        // Insert shipments
        foreach ($shipments as $shipment) {
            $pengirimanModel->insert($shipment);
        }

        // Insert shipment details
        foreach ($details as $detail) {
            $detailModel->insert($detail);
        }

        echo "  ✓ Created " . count($shipments) . " shipments with " . count($details) . " detail records\n";
    }

    /**
     * Prepare historical data for reporting
     */
    private function prepareHistoricalData(): void
    {
        echo "Creating historical data for reporting...\n";

        $pengirimanModel = new \App\Models\PengirimanModel();
        $detailModel = new \App\Models\DetailPengirimanModel();

        // Create additional historical data for the last 3 months
        $historicalShipments = [];
        $historicalDetails = [];

        for ($month = 1; $month <= 3; $month++) {
            for ($day = 1; $day <= 30; $day++) {
                $shipmentId = 'HST' . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($day, 2, '0', STR_PAD_LEFT);
                $date = date('Y-m-d', strtotime("-{$month} months -{$day} days"));
                $customerId = 'PLG' . str_pad(rand(1, 20), 3, '0', STR_PAD_LEFT);
                $courierId = 'KUR' . str_pad(rand(1, 10), 3, '0', STR_PAD_LEFT);
                $status = rand(2, 3); // Mostly completed shipments for historical data

                $historicalShipments[] = [
                    'id_pengiriman' => $shipmentId,
                    'tanggal' => $date,
                    'id_pelanggan' => $customerId,
                    'id_kurir' => $courierId,
                    'no_kendaraan' => 'B ' . rand(1000, 9999) . ' XYZ',
                    'no_po' => 'PO' . date('Ymd', strtotime($date)) . str_pad($day, 3, '0', STR_PAD_LEFT),
                    'keterangan' => 'Historical shipment - ' . $shipmentId,
                    'penerima' => 'Historical Receiver ' . $month . $day,
                    'photo' => null,
                    'status' => $status
                ];

                // Add items to historical shipments
                $itemCount = rand(2, 4);
                for ($j = 1; $j <= $itemCount; $j++) {
                    $itemId = 'BRG' . str_pad(rand(1, 50), 3, '0', STR_PAD_LEFT);
                    $quantity = rand(1, 8);

                    $historicalDetails[] = [
                        'id_pengiriman' => $shipmentId,
                        'id_barang' => $itemId,
                        'jumlah' => $quantity,
                        'keterangan' => 'Historical item detail'
                    ];
                }
            }
        }

        // Insert historical data in batches
        $batchSize = 50;
        $shipmentBatches = array_chunk($historicalShipments, $batchSize);
        $detailBatches = array_chunk($historicalDetails, $batchSize);

        foreach ($shipmentBatches as $batch) {
            $pengirimanModel->insertBatch($batch);
        }

        foreach ($detailBatches as $batch) {
            $detailModel->insertBatch($batch);
        }

        echo "  ✓ Created " . count($historicalShipments) . " historical shipments with " . count($historicalDetails) . " details\n";
    }

    /**
     * Generate test data report
     */
    private function generateTestDataReport(): void
    {
        $report = "\n=== TEST DATA PREPARATION REPORT ===\n";
        $report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";

        // Count records in each table
        $db = \Config\Database::connect();
        
        $tables = [
            'user' => 'User Accounts',
            'kategori' => 'Item Categories',
            'barang' => 'Inventory Items',
            'pelanggan' => 'Customer Accounts',
            'kurir' => 'Courier Accounts',
            'pengiriman' => 'Shipment Records',
            'detail_pengiriman' => 'Shipment Details'
        ];

        foreach ($tables as $table => $description) {
            $count = $db->table($table)->countAllResults();
            $report .= sprintf("%-20s: %d records\n", $description, $count);
        }

        $report .= "\n=== USER ACCOUNT CREDENTIALS ===\n";
        $report .= "Admin User    : adminpuninar / AdminPuninar123\n";
        $report .= "Kurir User   : kurirpuninar / KurirPuninar123\n";
        $report .= "Gudang User   : gudangpuninar / GudangPuninar123\n";
        $report .= "Test User     : testuser01 / TestUser123\n";

        $report .= "\n=== DATA DISTRIBUTION ===\n";
        $report .= "Categories    : 10 different categories\n";
        $report .= "Items per Cat : 5-6 items per category\n";
        $report .= "Customers     : 20 business customers\n";
        $report .= "Couriers      : 10 active couriers\n";
        $report .= "Recent Ship.  : 30 shipments (last 30 days)\n";
        $report .= "Historical    : 270 shipments (last 3 months)\n";

        $report .= "\n=== TESTING RECOMMENDATIONS ===\n";
        $report .= "1. Use different user accounts to test role-based access\n";
        $report .= "2. Test with various date ranges for reporting\n";
        $report .= "3. Use different customers and couriers for shipments\n";
        $report .= "4. Test with multiple items per shipment\n";
        $report .= "5. Verify data relationships and integrity\n";

        echo $report;

        // Save report to file
        $reportFile = WRITEPATH . 'test_data_report.txt';
        file_put_contents($reportFile, $report);
        echo "\nReport saved to: {$reportFile}\n";
    }

    /**
     * Clean up test data
     */
    public function cleanupTestData(): void
    {
        echo "Cleaning up test data...\n";

        $db = \Config\Database::connect();
        
        $tables = [
            'detail_pengiriman',
            'pengiriman',
            'barang',
            'kategori',
            'pelanggan',
            'kurir',
            'user'
        ];

        foreach ($tables as $table) {
            $db->table($table)->truncate();
            echo "  ✓ Cleaned table: {$table}\n";
        }

        echo "✅ Test data cleanup completed!\n";
    }

    /**
     * Verify test data integrity
     */
    public function verifyTestDataIntegrity(): bool
    {
        echo "Verifying test data integrity...\n";

        $db = \Config\Database::connect();
        $issues = [];

        // Check for orphaned records
        $orphanedDetails = $db->query("
            SELECT COUNT(*) as count 
            FROM detail_pengiriman dp 
            LEFT JOIN pengiriman p ON dp.id_pengiriman = p.id_pengiriman 
            WHERE p.id_pengiriman IS NULL
        ")->getRow()->count;

        if ($orphanedDetails > 0) {
            $issues[] = "Found {$orphanedDetails} orphaned detail_pengiriman records";
        }

        // Check for missing references
        $missingItems = $db->query("
            SELECT COUNT(*) as count 
            FROM detail_pengiriman dp 
            LEFT JOIN barang b ON dp.id_barang = b.id_barang 
            WHERE b.id_barang IS NULL
        ")->getRow()->count;

        if ($missingItems > 0) {
            $issues[] = "Found {$missingItems} detail records with missing items";
        }

        // Check for missing customers
        $missingCustomers = $db->query("
            SELECT COUNT(*) as count 
            FROM pengiriman p 
            LEFT JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan 
            WHERE pel.id_pelanggan IS NULL
        ")->getRow()->count;

        if ($missingCustomers > 0) {
            $issues[] = "Found {$missingCustomers} shipments with missing customers";
        }

        // Check for missing couriers
        $missingCouriers = $db->query("
            SELECT COUNT(*) as count 
            FROM pengiriman p 
            LEFT JOIN kurir k ON p.id_kurir = k.id_kurir 
            WHERE k.id_kurir IS NULL
        ")->getRow()->count;

        if ($missingCouriers > 0) {
            $issues[] = "Found {$missingCouriers} shipments with missing couriers";
        }

        if (empty($issues)) {
            echo "  ✅ All data integrity checks passed!\n";
            return true;
        } else {
            echo "  ❌ Data integrity issues found:\n";
            foreach ($issues as $issue) {
                echo "    - {$issue}\n";
            }
            return false;
        }
    }
}