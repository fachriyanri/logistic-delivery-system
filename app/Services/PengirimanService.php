<?php

namespace App\Services;

use App\Models\PengirimanModel;
use App\Models\DetailPengirimanModel;
use App\Models\PelangganModel;
use App\Models\KurirModel;
use App\Models\BarangModel;
use App\Entities\PengirimanEntity;

/**
 * Shipment Service
 * 
 * Business logic service for managing shipments (pengiriman) in the logistics system.
 * Handles CRUD operations, validation, status management, and related data operations
 * for shipments and their details.
 * 
 * @package App\Services
 * @author  CodeIgniter Logistics System
 * @version 1.0.0
 * @since   2024-01-01
 */
class PengirimanService
{
    /**
     * Shipment model for database operations
     * 
     * @var PengirimanModel
     */
    protected PengirimanModel $pengirimanModel;

    /**
     * Shipment detail model for item management
     * 
     * @var DetailPengirimanModel
     */
    protected DetailPengirimanModel $detailModel;

    /**
     * Customer model for customer data
     * 
     * @var PelangganModel
     */
    protected PelangganModel $pelangganModel;

    /**
     * Courier model for courier data
     * 
     * @var KurirModel
     */
    protected KurirModel $kurirModel;

    /**
     * Item model for inventory data
     * 
     * @var BarangModel
     */
    protected BarangModel $barangModel;

    /**
     * Constructor - Initialize model dependencies
     * 
     * Sets up all required model instances for shipment operations.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->pengirimanModel = new PengirimanModel();
        $this->detailModel = new DetailPengirimanModel();
        $this->pelangganModel = new PelangganModel();
        $this->kurirModel = new KurirModel();
        $this->barangModel = new BarangModel();
    }

    /**
     * Get all shipments with filtering and pagination
     * 
     * Retrieves shipments from database with optional filtering, pagination, and sorting.
     * Supports filtering by date range, status, customer, courier, and other criteria.
     * 
     * @param array  $filter    Filter criteria (date_from, date_to, status, customer_id, etc.)
     * @param int    $limit     Number of records to retrieve (default: 15)
     * @param int    $offset    Starting offset for pagination (default: 0)
     * @param string $orderBy   Column to sort by (default: 'id_pengiriman')
     * @param string $orderType Sort direction 'ASC' or 'DESC' (default: 'DESC')
     * 
     * @return array Array of shipment data with pagination info
     * 
     * @example
     * // Get recent shipments
     * $shipments = $service->getAllShipments();
     * 
     * @example
     * // Get filtered shipments
     * $filter = [
     *     'date_from' => '2024-01-01',
     *     'date_to' => '2024-01-31',
     *     'status' => 1
     * ];
     * $shipments = $service->getAllShipments($filter, 20, 0);
     * 
     * @see PengirimanModel::getAllWithFilter()
     */
    public function getAllShipments(array $filter = [], int $limit = 15, int $offset = 0, string $orderBy = 'id_pengiriman', string $orderType = 'DESC'): array
    {
        return $this->pengirimanModel->getAllWithFilter($filter, $limit, $offset, $orderBy, $orderType);
    }

    /**
     * Get shipment by ID with details
     */
    public function getShipmentById(string $id): ?PengirimanEntity
    {
        return $this->pengirimanModel->getWithDetails($id);
    }

    /**
     * Get shipment details
     */
    public function getShipmentDetails(string $id): array
    {
        return $this->detailModel->getByShipmentId($id);
    }

    /**
     * Create new shipment
     * 
     * Creates a new shipment with validation, transaction management, and detail items.
     * Generates unique shipment ID, validates all data, and ensures data integrity
     * through database transactions.
     * 
     * @param array $data    Shipment data (tanggal, id_pelanggan, id_kurir, etc.)
     * @param array $details Array of shipment detail items with id_barang and qty
     * 
     * @return array Result array with success status, message, and data
     *               Format: ['success' => bool, 'message' => string, 'data' => PengirimanEntity|null]
     * 
     * @throws \Exception When database operations fail
     * 
     * @example
     * // Create new shipment
     * $data = [
     *     'tanggal' => '2024-01-01',
     *     'id_pelanggan' => 'CST0001',
     *     'id_kurir' => 'KRR01',
     *     'no_kendaraan' => 'B1234ABC',
     *     'no_po' => 'PO123456'
     * ];
     * $details = [
     *     ['id_barang' => 'BRG0001', 'qty' => 5],
     *     ['id_barang' => 'BRG0002', 'qty' => 3]
     * ];
     * $result = $service->createShipment($data, $details);
     * 
     * @see validateShipmentData() Data validation
     * @see validateShipmentDetails() Detail validation
     * @see generateNextId() ID generation
     */
    public function createShipment(array $data, array $details): array
    {
        $result = ['success' => false, 'message' => '', 'data' => null];

        try {
            // Generate ID if not provided
            if (empty($data['id_pengiriman'])) {
                $data['id_pengiriman'] = $this->pengirimanModel->generateNextId();
            }

            // Validate shipment data
            $validationErrors = $this->validateShipmentData($data);
            if (!empty($validationErrors)) {
                $result['message'] = implode(', ', $validationErrors);
                return $result;
            }

            // Validate details
            $detailErrors = $this->validateShipmentDetails($details);
            if (!empty($detailErrors)) {
                $result['message'] = implode(', ', $detailErrors);
                return $result;
            }

            // Check if ID already exists
            if ($this->pengirimanModel->isIdExists($data['id_pengiriman'])) {
                $result['message'] = 'ID Pengiriman sudah terdaftar';
                return $result;
            }

            // Set default status if not provided
            if (empty($data['status'])) {
                $data['status'] = PengirimanEntity::STATUS_PENDING;
            }

            log_message('critical', 'DATA TO BE SAVED: ' . json_encode($data));

            // Start transaction
            $this->pengirimanModel->db->transStart();

            // Save shipment
            if (!$this->pengirimanModel->savePengiriman($data)) {
                throw new \Exception('Gagal menyimpan pengiriman');
            }

            // Save details
            if (!$this->detailModel->saveShipmentDetails($data['id_pengiriman'], $details)) {
                throw new \Exception('Gagal menyimpan detail pengiriman');
            }

            $this->pengirimanModel->db->transComplete();

            if ($this->pengirimanModel->db->transStatus()) {
                $result['success'] = true;
                $result['message'] = 'Pengiriman berhasil dibuat';
                $result['data'] = $this->getShipmentById($data['id_pengiriman']);
            } else {
                $result['message'] = 'Gagal menyimpan pengiriman';
            }

        } catch (\Exception $e) {
            $this->pengirimanModel->db->transRollback();
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Update existing shipment
     */
    public function updateShipment(string $id, array $data, array $details = []): array
    {
        $result = ['success' => false, 'message' => '', 'data' => null];

        try {
            // Check if shipment exists
            $existingShipment = $this->pengirimanModel->find($id);
            if (!$existingShipment) {
                $result['message'] = 'Pengiriman tidak ditemukan';
                return $result;
            }

            // Remove id_pengiriman from update data
            unset($data['id_pengiriman']);

            // Validate shipment data
            $data['id_pengiriman'] = $id; // Add for validation context
            $validationErrors = $this->validateShipmentData($data, $id);
            if (!empty($validationErrors)) {
                $result['message'] = implode(', ', $validationErrors);
                return $result;
            }
            unset($data['id_pengiriman']); // Remove again for update

            // Validate details if provided
            if (!empty($details)) {
                $detailErrors = $this->validateShipmentDetails($details);
                if (!empty($detailErrors)) {
                    $result['message'] = implode(', ', $detailErrors);
                    return $result;
                }
            }

            // Start transaction
            $this->pengirimanModel->db->transStart();

            // Update shipment
            if (!$this->pengirimanModel->savePengiriman($data, $id)) {
                throw new \Exception('Gagal memperbarui pengiriman');
            }

            // Update details if provided
            if (!empty($details)) {
                if (!$this->detailModel->saveShipmentDetails($id, $details)) {
                    throw new \Exception('Gagal memperbarui detail pengiriman');
                }
            }

            $this->pengirimanModel->db->transComplete();

            if ($this->pengirimanModel->db->transStatus()) {
                $result['success'] = true;
                $result['message'] = 'Pengiriman berhasil diperbarui';
                $result['data'] = $this->getShipmentById($id);
            } else {
                $result['message'] = 'Gagal memperbarui pengiriman';
            }

        } catch (\Exception $e) {
            $this->pengirimanModel->db->transRollback();
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Update shipment status
     */
    public function updateShipmentStatus(string $id, int $status, array $additionalData = []): array
    {
        $result = ['success' => false, 'message' => ''];

        try {
            // Check if shipment exists
            $shipment = $this->pengirimanModel->find($id);
            if (!$shipment) {
                $result['message'] = 'Pengiriman tidak ditemukan';
                return $result;
            }

            // Validate status
            if (!in_array($status, array_keys(PengirimanEntity::getStatusOptions()))) {
                $result['message'] = 'Status tidak valid';
                return $result;
            }

            // Update status
            if ($this->pengirimanModel->updateStatus($id, $status, $additionalData)) {
                $result['success'] = true;
                $result['message'] = 'Status pengiriman berhasil diperbarui';
            } else {
                $result['message'] = 'Gagal memperbarui status pengiriman';
            }

        } catch (\Exception $e) {
            $result['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Delete shipment
     */
    public function deleteShipment(string $id): array
    {
        return $this->pengirimanModel->deleteShipment($id);
    }

    /**
     * Generate next shipment ID
     */
    public function generateNextId(): string
    {
        return $this->pengirimanModel->generateNextId();
    }

    /**
     * Generate unique PO number
     * 
     * Generates a unique Purchase Order number with format: PO{YYYY}{MM}{DD}{XXX}
     * where XXX is a sequence number that ensures uniqueness by checking the database.
     * 
     * @return string Unique PO number
     * @throws \Exception If unable to generate unique number after maximum attempts
     */
    public function generatePONumber(): string
    {
        $db = \Config\Database::connect();
        $maxAttempts = 100;
        $attempt = 0;
        
        do {
            $attempt++;
            $now = new \DateTime();
            $year = $now->format('Y');
            $month = $now->format('m');
            $day = $now->format('d');
            
            // Use microseconds for better uniqueness
            $microtime = (int) ($now->format('u') / 1000); // Convert microseconds to milliseconds
            $sequence = str_pad($microtime % 1000, 3, '0', STR_PAD_LEFT);
            
            // If multiple attempts, use attempt number + random
            if ($attempt > 1) {
                $random = mt_rand(1, 999);
                $sequence = str_pad($random, 3, '0', STR_PAD_LEFT);
            }
            
            $poNumber = "PO{$year}{$month}{$day}{$sequence}";
            
            // Check if this PO number already exists
            $query = $db->query("SELECT COUNT(*) as count FROM pengiriman WHERE no_po = ?", [$poNumber]);
            $exists = $query->getRow()->count > 0;
            
            if (!$exists) {
                log_message('info', "Generated unique PO number: {$poNumber} (attempt {$attempt})");
                return $poNumber;
            }
            
            // If exists, wait a tiny bit and try again
            usleep(1000); // 1 millisecond
            
        } while ($exists && $attempt < $maxAttempts);
        
        // Fallback: use timestamp + random if all attempts failed
        $timestamp = time();
        $random = mt_rand(100, 999);
        $fallbackPO = "PO{$timestamp}{$random}";
        
        log_message('warning', "PO generation reached max attempts, using fallback: {$fallbackPO}");
        return $fallbackPO;
    }

    /**
     * Get customers for dropdown
     */
    public function getCustomersForSelect(): array
    {
        return $this->pelangganModel->orderBy('nama', 'ASC')->findAll();
    }

    /**
     * Get couriers for dropdown
     */
    public function getCouriersForSelect(): array
    {
        return $this->kurirModel->orderBy('nama', 'ASC')->findAll();
    }

    /**
     * Get items for dropdown
     */
    public function getItemsForSelect(): array
    {
        return $this->barangModel->orderBy('nama', 'ASC')->findAll();
    }

    /**
     * Validate shipment data
     */
    public function validateShipmentData(array $data, string $id = ''): array
    {
        $errors = [];

        // Validate required fields
        if (empty($data['tanggal'])) {
            $errors[] = 'Tanggal harus diisi';
        }

        if (empty($data['id_pelanggan'])) {
            $errors[] = 'Pelanggan harus dipilih';
        }

        if (empty($data['id_kurir'])) {
            $errors[] = 'Kurir harus dipilih';
        }

        if (empty($data['no_po'])) {
            $errors[] = 'Nomor PO harus diisi';
        }

        if (empty($data['no_kendaraan'])) {
            $errors[] = 'Nomor kendaraan harus diisi';
        }

        // Validate ID uniqueness (only for new records or when ID changes)
        if (!empty($data['id_pengiriman']) && $data['id_pengiriman'] !== $id) {
            $existing = $this->pengirimanModel->find($data['id_pengiriman']);
            if ($existing) {
                $errors[] = 'ID Pengiriman sudah terdaftar';
            }
        }

        // Validate date format
        if (!empty($data['tanggal'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $data['tanggal']);
            if (!$date || $date->format('Y-m-d') !== $data['tanggal']) {
                $errors[] = 'Format tanggal tidak valid (YYYY-MM-DD)';
            }
        }

        // Validate field lengths
        if (!empty($data['no_po']) && strlen($data['no_po']) > 15) {
            $errors[] = 'Nomor PO maksimal 15 karakter';
        }

        if (!empty($data['no_kendaraan']) && strlen($data['no_kendaraan']) > 8) {
            $errors[] = 'Nomor kendaraan maksimal 8 karakter';
        }

        // Validate status if provided
        if (!empty($data['status']) && !in_array($data['status'], array_keys(PengirimanEntity::getStatusOptions()))) {
            $errors[] = 'Status tidak valid';
        }

        // Validate status requirements
        // Note: penerima field has been removed from forms as per user request
        // Only keterangan is required for non-pending statuses
        if (!empty($data['status']) && $data['status'] != PengirimanEntity::STATUS_PENDING) {
            if (empty($data['keterangan'])) {
                $errors[] = 'Keterangan harus diisi untuk status ini';
            }
        }

        return $errors;
    }

    /**
     * Validate shipment details
     */
    public function validateShipmentDetails(array $details): array
    {
        $errors = [];

        // Debug logging
        log_message('debug', 'PengirimanService::validateShipmentDetails - Details received: ' . json_encode($details));

        if (empty($details)) {
            $errors[] = 'Detail barang harus diisi';
            log_message('debug', 'PengirimanService::validateShipmentDetails - Details array is empty');
            return $errors;
        }

        foreach ($details as $index => $detail) {
            log_message('debug', "PengirimanService::validateShipmentDetails - Validating detail {$index}: " . json_encode($detail));
            $detailErrors = $this->detailModel->validateDetailData($detail);
            if (!empty($detailErrors)) {
                log_message('debug', "PengirimanService::validateShipmentDetails - Detail {$index} errors: " . json_encode($detailErrors));
                $errors[] = "Item " . ($index + 1) . ": " . implode(', ', $detailErrors);
            }
        }

        log_message('debug', 'PengirimanService::validateShipmentDetails - Final errors: ' . json_encode($errors));
        return $errors;
    }

    /**
     * Get shipment statistics
     */
    public function getShipmentStatistics(array $dateRange = []): array
    {
        return $this->pengirimanModel->getShipmentStatistics($dateRange);
    }

    /**
     * Get shipments for export
     */
    public function getShipmentsForExport(array $filter = []): array
    {
        return $this->pengirimanModel->getShipmentsForExport($filter);
    }

    /**
     * Get shipments by date range
     */
    public function getShipmentsByDateRange(string $from, string $to, array $filters = []): array
    {
        return $this->pengirimanModel->getShipmentsByDateRange($from, $to, $filters);
    }

    /**
     * Get status options for forms
     */
    public function getStatusOptions(): array
    {
        return PengirimanEntity::getStatusOptions();
    }
}