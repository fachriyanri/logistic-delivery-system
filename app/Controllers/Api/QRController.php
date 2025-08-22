<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\QRCodeService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * QR Code API Controller
 * Handles QR code validation and mobile scanning endpoints
 */
class QRController extends BaseController
{
    protected $qrService;

    public function __construct()
    {
        $this->qrService = new QRCodeService();
    }

    /**
     * Validate QR code data
     */
    public function validate(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        $qrData = $this->request->getJSON(true)['qr_data'] ?? '';
        
        if (empty($qrData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'QR data is required'
            ]);
        }

        try {
            $validation = $this->qrService->validateQRData($qrData);
            
            if ($validation['valid']) {
                // Additional validation based on type
                switch ($validation['type']) {
                    case 'shipment_tracking':
                    case 'shipment_id':
                        $shipmentExists = $this->validateShipmentExists($validation['shipment_id']);
                        if (!$shipmentExists) {
                            return $this->response->setJSON([
                                'success' => false,
                                'valid' => false,
                                'message' => 'Shipment not found'
                            ]);
                        }
                        break;
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'valid' => true,
                    'type' => $validation['type'],
                    'shipment_id' => $validation['shipment_id'] ?? null,
                    'message' => 'Valid QR code'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => true,
                    'valid' => false,
                    'message' => $validation['error'] ?? 'Invalid QR code format'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'QR validation error: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Validation failed'
            ]);
        }
    }

    /**
     * Generate QR code for shipment
     */
    public function generate(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        $data = $this->request->getJSON(true);
        $shipmentId = $data['shipment_id'] ?? '';
        $options = $data['options'] ?? [];

        if (empty($shipmentId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Shipment ID is required'
            ]);
        }

        try {
            // Validate shipment exists
            if (!$this->validateShipmentExists($shipmentId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Shipment not found'
                ]);
            }

            $result = $this->qrService->generateShipmentQR($shipmentId, $options);
            
            return $this->response->setJSON($result);
            
        } catch (\Exception $e) {
            log_message('error', 'QR generation error: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'QR code generation failed'
            ]);
        }
    }

    /**
     * Get mobile scanner configuration
     */
    public function scannerConfig(): ResponseInterface
    {
        try {
            $config = $this->qrService->getMobileScannerConfig();
            
            return $this->response->setJSON([
                'success' => true,
                'config' => $config
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Scanner config error: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to get scanner configuration'
            ]);
        }
    }

    /**
     * Track shipment via QR scan
     */
    public function track(string $shipmentId = ''): ResponseInterface
    {
        if (empty($shipmentId)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Shipment ID is required'
            ]);
        }

        try {
            // Load shipment model
            $pengirimanModel = model('PengirimanModel');
            $shipment = $pengirimanModel->getWithDetails($shipmentId);
            
            if (!$shipment) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Shipment not found'
                ]);
            }

            // Prepare tracking data
            $trackingData = [
                'shipment_id' => $shipment->id_pengiriman,
                'date' => $shipment->tanggal,
                'customer' => $shipment->pelanggan_nama ?? 'N/A',
                'courier' => $shipment->kurir_nama ?? 'N/A',
                'vehicle' => $shipment->no_kendaraan,
                'po_number' => $shipment->no_po,
                'status' => $shipment->getStatusText(),
                'recipient' => $shipment->penerima,
                'notes' => $shipment->keterangan,
                'items' => []
            ];

            // Load shipment items
            $detailModel = model('DetailPengirimanModel');
            $items = $detailModel->where('id_pengiriman', $shipmentId)->findAll();
            
            foreach ($items as $item) {
                $trackingData['items'][] = [
                    'name' => $item->barang_nama ?? 'N/A',
                    'quantity' => $item->jumlah,
                    'unit' => $item->satuan ?? 'pcs',
                    'notes' => $item->keterangan ?? ''
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'shipment' => $trackingData
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Shipment tracking error: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to retrieve shipment information'
            ]);
        }
    }

    /**
     * Mobile-friendly tracking page
     */
    public function mobileTrack(string $shipmentId = ''): string
    {
        if (empty($shipmentId)) {
            return view('errors/html/error_404');
        }

        try {
            // Get tracking data via API
            $trackingResponse = $this->track($shipmentId);
            $trackingData = json_decode($trackingResponse->getBody(), true);
            
            if (!$trackingData['success']) {
                return view('errors/html/error_404');
            }

            $data = [
                'title' => 'Track Shipment - ' . $shipmentId,
                'shipment' => $trackingData['shipment'],
                'is_mobile' => true
            ];

            return view('mobile/track_shipment', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Mobile tracking error: ' . $e->getMessage());
            return view('errors/html/error_500');
        }
    }

    /**
     * Cleanup old QR codes
     */
    public function cleanup(): ResponseInterface
    {
        // Only allow from CLI or admin users
        if (!is_cli() && (!session('level') || session('level') != 1)) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        try {
            $daysOld = $this->request->getGet('days') ?? 30;
            $deleted = $this->qrService->cleanupOldQRCodes((int)$daysOld);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Cleaned up {$deleted} old QR code files",
                'deleted_count' => $deleted
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'QR cleanup error: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Cleanup failed'
            ]);
        }
    }

    /**
     * Validate if shipment exists
     */
    protected function validateShipmentExists(string $shipmentId): bool
    {
        try {
            $pengirimanModel = model('PengirimanModel');
            $shipment = $pengirimanModel->find($shipmentId);
            return $shipment !== null;
        } catch (\Exception $e) {
            log_message('error', 'Shipment validation error: ' . $e->getMessage());
            return false;
        }
    }
}