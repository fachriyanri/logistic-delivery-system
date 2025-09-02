<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\PengirimanService;
use CodeIgniter\HTTP\ResponseInterface;

class PengirimanController extends BaseController
{
    protected PengirimanService $pengirimanService;

    public function __construct()
    {
        $this->pengirimanService = new PengirimanService();
    }

    /**
     * Display list of shipments
     */
    public function index(): string
    {
        // Get filter parameters
        $search = trim($this->request->getGet('search') ?? '');
        $status = trim($this->request->getGet('status') ?? '');
        $tanggal_dari = trim($this->request->getGet('tanggal_dari') ?? '');
        $tanggal_sampai = trim($this->request->getGet('tanggal_sampai') ?? '');

        $filter = [
            'keyword' => $search,
            'status' => $status,
            'from' => $tanggal_dari,
            'to' => $tanggal_sampai
        ];

        // Remove empty filters to prevent showing data when search should return no results
        $filter = array_filter($filter, function($value) {
            return $value !== '' && $value !== null;
        });

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        // Get shipments data
        [$pengiriman, $total] = $this->pengirimanService->getAllShipments($filter, $perPage, $offset);

        // Setup pagination
        $pager = \Config\Services::pager();
        $pager->setPath('pengiriman');
        $pager->makeLinks($page, $perPage, $total);

        $data = [
            'title' => 'Data Pengiriman',
            'pengiriman' => $pengiriman,
            'pager' => $pager,
            'total' => $total,
            'search' => $search,
            'status' => $status,
            'tanggal_dari' => $tanggal_dari,
            'tanggal_sampai' => $tanggal_sampai,
            'currentPage' => $page,
            'perPage' => $perPage
        ];

        return view('pengiriman/index', $data);
    }

    /**
     * Show form for creating/editing shipment
     */
    public function manage(?string $id = null): string
    {
        $data = [
            'title' => $id ? 'Edit Pengiriman' : 'Tambah Pengiriman',
            'isEdit' => !empty($id),
            'pengiriman' => null,
            'detail_pengiriman' => [],
            'pelanggan' => $this->pengirimanService->getCustomersForSelect(),
            'kurir' => $this->pengirimanService->getCouriersForSelect(),
            'barang' => $this->pengirimanService->getItemsForSelect(),
            'statusOptions' => $this->pengirimanService->getStatusOptions(),
            'autocode' => $this->pengirimanService->generateNextId(),
            'autoPO' => $id ? null : $this->pengirimanService->generatePONumber()
        ];

        if ($id) {
            $pengiriman = $this->pengirimanService->getShipmentById($id);
            if (!$pengiriman) {
                session()->setFlashdata('error', 'Pengiriman tidak ditemukan');
                return redirect()->to('/pengiriman');
            }
            $data['pengiriman'] = $pengiriman;
            $data['detail_pengiriman'] = $this->pengirimanService->getShipmentDetails($id);
        }

        return view('pengiriman/manage', $data);
    }

    /**
     * Save shipment (create or update)
     */
    public function save(): ResponseInterface
    {
        $post = $this->request->getPost();
        
        if (!$post) {
            return redirect()->to('/pengiriman');
        }

        $id = $post['id'] ?? '';
        $action = $post['action'] ?? 'save';

        // Prepare shipment data based on user role
        $userLevel = session('level');
        
        $data = [
            'id_pengiriman' => $post['id_pengiriman'] ?? '',
            'tanggal' => $post['tanggal'] ?? '',
            'id_pelanggan' => $post['id_pelanggan'] ?? '',
            'id_kurir' => $post['id_kurir'] ?? '',
            'no_po' => $post['no_po'] ?? '',
            'no_kendaraan' => $post['no_kendaraan'] ?? '',
            'status' => (int) ($post['status'] ?? 1),
            'keterangan' => $post['keterangan'] ?? '',
            'detail_location' => $post['detail_location'] ?? ''
        ];
        
        // Only add penerima field for courier users (level 2)
        if ($userLevel == 2) {
            $data['penerima'] = $post['penerima'] ?? '';
        }

        // Prepare details data
        $details = [];
        if (!empty($post['items']) && is_array($post['items'])) {
            foreach ($post['items'] as $item) {
                if (!empty($item['id_barang']) && !empty($item['qty'])) {
                    $details[] = [
                        'id_barang' => $item['id_barang'],
                        'qty' => (int) $item['qty']
                        // Note: keterangan field doesn't exist in detail_pengiriman table
                    ];
                }
            }
        }

        // Save shipment
        if (empty($id)) {
            // Create new shipment
            $result = $this->pengirimanService->createShipment($data, $details);
        } else {
            // Update existing shipment
            $result = $this->pengirimanService->updateShipment($id, $data, $details);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            
            if ($action === 'save') {
                $redirectId = $result['data']->id_pengiriman ?? $id;
                return redirect()->to('/pengiriman/manage/' . $redirectId);
            } else {
                return redirect()->to('/pengiriman');
            }
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/pengiriman/manage/' . $id)->withInput();
        }
    }

    /**
     * Update shipment status
     */
    public function updateStatus(?string $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID pengiriman tidak valid'
            ]);
        }

        $status = (int) $this->request->getPost('status');
        $penerima = $this->request->getPost('penerima') ?? '';
        $keterangan = $this->request->getPost('keterangan') ?? '';

        $additionalData = [];
        if (!empty($penerima)) {
            $additionalData['penerima'] = $penerima;
        }
        if (!empty($keterangan)) {
            $additionalData['keterangan'] = $keterangan;
        }

        $result = $this->pengirimanService->updateShipmentStatus($id, $status, $additionalData);
        
        return $this->response->setJSON($result);
    }

    /**
     * Delete shipment
     */
    public function delete(?string $id = null): ResponseInterface
    {
        if (empty($id)) {
            session()->setFlashdata('error', 'ID pengiriman tidak valid');
            return redirect()->to('/pengiriman');
        }

        $result = $this->pengirimanService->deleteShipment($id);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/pengiriman');
    }

    /**
     * Generate delivery note
     */
    public function deliveryNote(?string $id = null): string
    {
        if (empty($id)) {
            session()->setFlashdata('error', 'ID pengiriman tidak valid');
            return redirect()->to('/pengiriman');
        }

        $pengiriman = $this->pengirimanService->getShipmentById($id);
        if (!$pengiriman) {
            session()->setFlashdata('error', 'Pengiriman tidak ditemukan');
            return redirect()->to('/pengiriman');
        }

        $details = $this->pengirimanService->getShipmentDetails($id);

        $data = [
            'title' => 'Surat Jalan - ' . $pengiriman->id_pengiriman,
            'pengiriman' => $pengiriman,
            'details' => $details
        ];

        return view('pengiriman/delivery_note', $data);
    }

    /**
     * AJAX endpoint to generate next shipment ID
     */
    public function generateCode(): ResponseInterface
    {
        $nextId = $this->pengirimanService->generateNextId();
        
        return $this->response->setJSON([
            'success' => true,
            'code' => $nextId
        ]);
    }

    /**
     * AJAX endpoint to generate unique PO number
     */
    public function generatePO(): ResponseInterface
    {
        try {
            $poNumber = $this->pengirimanService->generatePONumber();
            
            return $this->response->setJSON([
                'success' => true,
                'po_number' => $poNumber
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to generate PO number: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal generate nomor PO'
            ]);
        }
    }

    /**
     * Debug endpoint to capture POST data
     */
    public function debugStore(): ResponseInterface
    {
        $post = $this->request->getPost();
        $raw = $this->request->getRawInput();
        
        $debug = [
            'method' => $this->request->getMethod(),
            'post_data' => $post,
            'raw_input' => $raw,
            'content_type' => $this->request->getHeaderLine('Content-Type'),
            'items_analysis' => []
        ];
        
        if (isset($post['items'])) {
            foreach ($post['items'] as $index => $item) {
                $debug['items_analysis'][$index] = [
                    'id_barang' => $item['id_barang'] ?? 'missing',
                    'qty' => $item['qty'] ?? 'missing',
                    'keterangan' => $item['keterangan'] ?? 'missing',
                    'valid' => !empty($item['id_barang']) && !empty($item['qty'])
                ];
            }
        }
        
        return $this->response->setJSON($debug);
    }



    /**
     * Get shipment details (AJAX)
     */
    public function getDetails(): ResponseInterface
    {
        $id = $this->request->getGet('id');
        
        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID pengiriman tidak valid'
            ]);
        }

        $pengiriman = $this->pengirimanService->getShipmentById($id);
        if (!$pengiriman) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pengiriman tidak ditemukan'
            ]);
        }

        $details = $this->pengirimanService->getShipmentDetails($id);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'pengiriman' => $pengiriman->getSummary(),
                'details' => array_map(function($detail) {
                    return $detail->getItemSummary();
                }, $details)
            ]
        ]);
    }

    /**
     * Get shipment statistics (AJAX)
     */
    public function statistics(): ResponseInterface
    {
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');
        
        $dateRange = [];
        if (!empty($from) && !empty($to)) {
            $dateRange = ['from' => $from, 'to' => $to];
        }

        $stats = $this->pengirimanService->getShipmentStatistics($dateRange);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Show create form
     */
    public function create(): string
    {
        return $this->manage();
    }

    /**
     * Store new shipment
     */
    public function store(): ResponseInterface
    {
        $post = $this->request->getPost();
        
        // CRITICAL DEBUG: Log the raw POST data
        log_message('critical', 'PengirimanController::store - RAW POST DATA: ' . json_encode($post));
        
        if (!$post) {
            log_message('critical', 'PengirimanController::store - No POST data received');
            return redirect()->to('/pengiriman');
        }

        // Auto-generate PO number if empty
        $noPO = $post['no_po'] ?? '';
        if (empty($noPO)) {
            $noPO = $this->pengirimanService->generatePONumber();
        }

        // Prepare shipment data
        $data = [
            'id_pengiriman' => $post['id_pengiriman'] ?? $this->pengirimanService->generateNextId(),
            'tanggal' => $post['tanggal'] ?? date('Y-m-d'),
            'id_pelanggan' => $post['id_pelanggan'] ?? '',
            'id_kurir' => $post['id_kurir'] ?? '',
            'no_po' => $noPO,
            'no_kendaraan' => $post['no_kendaraan'] ?? '',
            'status' => (int) ($post['status'] ?? 1),
            'keterangan' => $post['keterangan'] ?? ''
        ];

        // Prepare details data
        $details = [];
        if (!empty($post['items']) && is_array($post['items'])) {
            foreach ($post['items'] as $item) {
                if (!empty($item['id_barang']) && !empty($item['qty'])) {
                    $details[] = [
                        'id_barang' => $item['id_barang'],
                        'qty' => (int) $item['qty'] // Map jumlah to qty
                        // Note: keterangan field doesn't exist in detail_pengiriman table
                    ];
                }
            }
        }

        // Debug logging
        log_message('debug', 'PengirimanController::store - POST data: ' . json_encode($post));
        log_message('debug', 'PengirimanController::store - Prepared details: ' . json_encode($details));

        // Create shipment
        $result = $this->pengirimanService->createShipment($data, $details);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            return redirect()->to('/pengiriman');
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/pengiriman/create')->withInput();
        }
    }

    /**
     * Show shipment detail
     */
    public function detail(?string $id = null): string
    {
        if (empty($id)) {
            session()->setFlashdata('error', 'ID pengiriman tidak valid');
            return redirect()->to('/pengiriman');
        }

        $pengiriman = $this->pengirimanService->getShipmentById($id);
        if (!$pengiriman) {
            session()->setFlashdata('error', 'Pengiriman tidak ditemukan');
            return redirect()->to('/pengiriman');
        }

        $details = $this->pengirimanService->getShipmentDetails($id);

        $data = [
            'title' => 'Detail Pengiriman - ' . $pengiriman->id_pengiriman,
            'pengiriman' => $pengiriman,
            'detail_pengiriman' => $details
        ];

        return view('pengiriman/detail', $data);
    }

    /**
     * Show edit form
     */
    public function edit(?string $id = null): string
    {
        if (empty($id)) {
            session()->setFlashdata('error', 'ID pengiriman tidak valid');
            return redirect()->to('/pengiriman');
        }

        $pengiriman = $this->pengirimanService->getShipmentById($id);
        if (!$pengiriman) {
            session()->setFlashdata('error', 'Pengiriman tidak ditemukan');
            return redirect()->to('/pengiriman');
        }

        $details = $this->pengirimanService->getShipmentDetails($id);

        $data = [
            'title' => 'Edit Pengiriman - ' . $pengiriman->id_pengiriman,
            'pengiriman' => $pengiriman,
            'detail_pengiriman' => $details,
            'pelanggan' => $this->pengirimanService->getCustomersForSelect(),
            'kurir' => $this->pengirimanService->getCouriersForSelect(),
            'barang' => $this->pengirimanService->getItemsForSelect()
        ];

        return view('pengiriman/manage', $data);
    }

    /**
     * Update shipment
     */
    public function update(?string $id = null): ResponseInterface
    {
        if (empty($id)) {
            log_message('error', 'PengirimanController::update - Empty shipment ID provided');
            session()->setFlashdata('error', 'ID pengiriman tidak valid');
            return redirect()->to('/pengiriman');
        }

        log_message('info', "PengirimanController::update - Starting update for shipment ID: {$id}");
        
        $post = $this->request->getPost();
        log_message('debug', 'PengirimanController::update - POST data received: ' . json_encode($post));
        
        // Check user level and restrict data accordingly
        $userLevel = session('level');
        log_message('debug', 'PengirimanController::update - User Level: ' . $userLevel);
        
        if ($userLevel == 2) {
            // Level 2 (Kurir) can only update status, detail_location, and penerima
            $data = [
                'status' => (int) ($post['status'] ?? 1),
                'detail_location' => $post['detail_location'] ?? '',
                'penerima' => $post['penerima'] ?? ''
            ];
            
            // Don't allow details update for couriers
            $details = [];
            
            log_message('debug', 'PengirimanController::update - Courier restricted update: ' . json_encode($data));
            log_message('debug', 'PengirimanController::update - Session level check: ' . session('level') . ' == 2');
        } else {
            // Admin/Gudang can update all fields except penerima (that's only for couriers)
            $data = [
                'tanggal' => $post['tanggal'] ?? '',
                'id_pelanggan' => $post['id_pelanggan'] ?? '',
                'id_kurir' => $post['id_kurir'] ?? '',
                'no_po' => $post['no_po'] ?? '',
                'detail_location' => $post['detail_location'] ?? '',
                'no_kendaraan' => $post['no_kendaraan'] ?? '',
                'status' => (int) ($post['status'] ?? 1),
                'keterangan' => $post['keterangan'] ?? ''
            ];
            
            // Prepare details data for non-courier users
            $details = [];
            if (!empty($post['items']) && is_array($post['items'])) {
                foreach ($post['items'] as $index => $item) {
                    if (!empty($item['id_barang']) && !empty($item['qty'])) {
                        $details[] = [
                            'id_barang' => $item['id_barang'],
                            'qty' => (int) $item['qty']
                        ];
                        log_message('debug', "PengirimanController::update - Added detail item {$index}: barang={$item['id_barang']}, qty={$item['qty']}");
                    }
                }
            }
        }
        
        log_message('debug', 'PengirimanController::update - Prepared details: ' . json_encode($details));

        // Update shipment
        log_message('info', "PengirimanController::update - Calling service to update shipment {$id}");
        log_message('debug', 'PengirimanController::update - Final data to service: ' . json_encode($data));
        log_message('debug', 'PengirimanController::update - User level to service: ' . $userLevel);
        $result = $this->pengirimanService->updateShipment($id, $data, $details);
        
        log_message('info', 'PengirimanController::update - Service result: ' . json_encode($result));

        if ($result['success']) {
            log_message('info', "PengirimanController::update - Successfully updated shipment {$id}");
            session()->setFlashdata('success', $result['message']);
            return redirect()->to('/pengiriman/detail/' . $id);
        } else {
            log_message('error', "PengirimanController::update - Failed to update shipment {$id}: {$result['message']}");
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/pengiriman/edit/' . $id)->withInput();
        }
    }

    

    /**
     * Generate QR Code
     */
    public function generateQR(?string $id = null): ResponseInterface
    {
        if (empty($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'ID pengiriman tidak valid'
            ]);
        }

        $pengiriman = $this->pengirimanService->getShipmentById($id);
        if (!$pengiriman) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Pengiriman tidak ditemukan'
            ]);
        }

        // Generate QR code for tracking URL
        $trackingUrl = base_url('track/' . $id);
        
        // Use QR Code service
        $qrService = new \App\Services\QRCodeService();
        $options = [
            'size' => 'L',
            'errorCorrection' => 'M',
            'format' => 'png',
            'mobileOptimized' => false,
            'includeUrl' => true
        ];
        $filename = $qrService->generateQRCode($trackingUrl, $id, $options);

        if ($filename) {
            $filepath = WRITEPATH . 'uploads/qrcodes/' . $filename;
            if (file_exists($filepath)) {
                $qrCodeData = file_get_contents($filepath);
                unlink($filepath); // Clean up the generated file

                return $this->response
                    ->setHeader('Content-Type', 'image/png')
                    ->setHeader('Content-Disposition', 'inline; filename="qr-' . $id . '.png"')
                    ->setBody($qrCodeData);
            }
        }
        
        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Gagal generate QR code'
        ]);
    }

    /**
     * Track shipment
     */
    public function track(?string $id = null): string
    {
        if (empty($id)) {
            session()->setFlashdata('error', 'ID pengiriman tidak valid');
            return redirect()->to('/pengiriman');
        }

        $pengiriman = $this->pengirimanService->getShipmentById($id);
        if (!$pengiriman) {
            session()->setFlashdata('error', 'Pengiriman tidak ditemukan');
            return redirect()->to('/pengiriman');
        }

        $details = $this->pengirimanService->getShipmentDetails($id);

        // Convert to format expected by mobile view
        $statusMap = [
            1 => 'Pending',
            2 => 'In Transit',
            3 => 'Delivered',
            4 => 'Cancelled'
        ];

        $items = [];
        foreach ($details as $detail) {
            $items[] = [
                'name' => $detail->nama_barang ?? 'N/A',
                'quantity' => $detail->qty,
                'unit' => $detail->satuan ?? 'Unit',
                'notes' => $detail->keterangan ?? ''
            ];
        }

        $shipment = [
            'shipment_id' => $pengiriman->id_pengiriman,
            'status' => $statusMap[$pengiriman->status] ?? 'Unknown',
            'date' => $pengiriman->tanggal,
            'customer' => $pengiriman->nama_pelanggan ?? 'N/A',
            'courier' => $pengiriman->nama_kurir ?? 'N/A',
            'vehicle' => $pengiriman->no_kendaraan ?? '',
            'po_number' => $pengiriman->no_po ?? '',
            'detail_location' => $pengiriman->detail_location ?? '',
            'recipient' => $pengiriman->penerima ?? '',
            'notes' => $pengiriman->keterangan ?? '',
            'items' => $items
        ];

        $data = [
            'title' => 'Tracking Pengiriman - ' . $pengiriman->id_pengiriman,
            'shipment' => $shipment
        ];

        return view('mobile/track_shipment', $data);
    }

    /**
     * Export shipments to Excel
     */
    public function export(): ResponseInterface
    {
        // Get filter parameters with same mapping as index method
        $search = trim($this->request->getGet('search') ?? '');
        $status = trim($this->request->getGet('status') ?? '');
        $tanggal_dari = trim($this->request->getGet('tanggal_dari') ?? '');
        $tanggal_sampai = trim($this->request->getGet('tanggal_sampai') ?? '');

        $filter = [
            'keyword' => $search,
            'status' => $status,
            'from' => $tanggal_dari,
            'to' => $tanggal_sampai
        ];

        // Remove empty filters
        $filter = array_filter($filter, function($value) {
            return $value !== '' && $value !== null;
        });

        // Get all shipments for export
        [$shipments, $total] = $this->pengirimanService->getAllShipments($filter, 0, 0);

        // Create Excel file using PhpSpreadsheet
        $filename = 'pengiriman_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Set proper headers for Excel download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Create new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set column headers
        $sheet->setCellValue('A1', 'ID Pengiriman')
              ->setCellValue('B1', 'Tanggal')
              ->setCellValue('C1', 'No PO')
              ->setCellValue('D1', 'Pelanggan')
              ->setCellValue('E1', 'Kurir')
              ->setCellValue('F1', 'No Kendaraan')
              ->setCellValue('G1', 'Penerima')
              ->setCellValue('H1', 'Status')
              ->setCellValue('I1', 'Keterangan');
        
        // Add data
        $row = 2;
        foreach ($shipments as $shipment) {
            $statusText = '';
            switch ($shipment->status) {
                case 1: $statusText = 'Pending'; break;
                case 2: $statusText = 'Dalam Perjalanan'; break;
                case 3: $statusText = 'Terkirim'; break;
                case 4: $statusText = 'Dibatalkan'; break;
                default: $statusText = 'Unknown'; break;
            }
            
            $sheet->setCellValue('A' . $row, $shipment->id_pengiriman)
                  ->setCellValue('B' . $row, date('d/m/Y', strtotime($shipment->tanggal)))
                  ->setCellValue('C' . $row, $shipment->no_po ?? '')
                  ->setCellValue('D' . $row, $shipment->nama_pelanggan ?? '')
                  ->setCellValue('E' . $row, $shipment->nama_kurir ?? '')
                  ->setCellValue('F' . $row, $shipment->no_kendaraan ?? '')
                  ->setCellValue('G' . $row, $shipment->penerima ?? '')
                  ->setCellValue('H' . $row, $statusText)
                  ->setCellValue('I' . $row, $shipment->keterangan ?? '');
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Freeze the first row
        $sheet->freezePane('A2');
        
        // Style the header row
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ]
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        
        // Redirect output to a client's web browser (Xlsx)
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}