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
            'autocode' => $this->pengirimanService->generateNextId()
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

        // Prepare shipment data
        $data = [
            'id_pengiriman' => $post['id_pengiriman'] ?? '',
            'tanggal' => $post['tanggal'] ?? '',
            'id_pelanggan' => $post['id_pelanggan'] ?? '',
            'id_kurir' => $post['id_kurir'] ?? '',
            'no_po' => $post['no_po'] ?? '',
            'no_kendaraan' => $post['no_kendaraan'] ?? '',
            'status' => (int) ($post['status'] ?? 1),
            'keterangan' => $post['keterangan'] ?? '',
            'penerima' => $post['penerima'] ?? ''
        ];

        // Prepare details data
        $details = [];
        if (!empty($post['items']) && is_array($post['items'])) {
            foreach ($post['items'] as $item) {
                if (!empty($item['id_barang']) && !empty($item['jumlah'])) {
                    $details[] = [
                        'id_barang' => $item['id_barang'],
                        'qty' => (int) $item['jumlah'],
                        'keterangan' => $item['keterangan'] ?? ''
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
        
        if (!$post) {
            return redirect()->to('/pengiriman');
        }

        // Prepare shipment data
        $data = [
            'id_pengiriman' => $post['id_pengiriman'] ?? $this->pengirimanService->generateNextId(),
            'tanggal' => $post['tanggal'] ?? date('Y-m-d'),
            'id_pelanggan' => $post['id_pelanggan'] ?? '',
            'id_kurir' => $post['id_kurir'] ?? '',
            'no_po' => $post['no_po'] ?? '',
            'no_kendaraan' => $post['no_kendaraan'] ?? '',
            'status' => (int) ($post['status'] ?? 1),
            'keterangan' => $post['keterangan'] ?? '',
            'penerima' => $post['penerima'] ?? ''
        ];

        // Prepare details data
        $details = [];
        if (!empty($post['items']) && is_array($post['items'])) {
            foreach ($post['items'] as $item) {
                if (!empty($item['id_barang']) && !empty($item['jumlah'])) {
                    $details[] = [
                        'id_barang' => $item['id_barang'],
                        'jumlah' => (int) $item['jumlah'],
                        'keterangan' => $item['keterangan'] ?? ''
                    ];
                }
            }
        }

        // Handle file upload
        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move(WRITEPATH . '../public/uploads/pengiriman/', $newName);
            $data['photo'] = $newName;
        }

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
            session()->setFlashdata('error', 'ID pengiriman tidak valid');
            return redirect()->to('/pengiriman');
        }

        $post = $this->request->getPost();
        
        // Prepare shipment data
        $data = [
            'tanggal' => $post['tanggal'] ?? '',
            'id_pelanggan' => $post['id_pelanggan'] ?? '',
            'id_kurir' => $post['id_kurir'] ?? '',
            'no_po' => $post['no_po'] ?? '',
            'no_kendaraan' => $post['no_kendaraan'] ?? '',
            'status' => (int) ($post['status'] ?? 1),
            'keterangan' => $post['keterangan'] ?? '',
            'penerima' => $post['penerima'] ?? ''
        ];

        // Prepare details data
        $details = [];
        if (!empty($post['items']) && is_array($post['items'])) {
            foreach ($post['items'] as $item) {
                if (!empty($item['id_barang']) && !empty($item['jumlah'])) {
                    $details[] = [
                        'id_barang' => $item['id_barang'],
                        'jumlah' => (int) $item['jumlah'],
                        'keterangan' => $item['keterangan'] ?? ''
                    ];
                }
            }
        }

        // Handle file upload
        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move(WRITEPATH . '../public/uploads/pengiriman/', $newName);
            $data['photo'] = $newName;
        }

        // Update shipment
        $result = $this->pengirimanService->updateShipment($id, $data, $details);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            return redirect()->to('/pengiriman/detail/' . $id);
        } else {
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
     * Duplicate shipment
     */
    public function duplicate(?string $id = null): string
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

        // Reset some fields for duplication
        $pengiriman->id_pengiriman = $this->pengirimanService->generateNextId();
        $pengiriman->tanggal = date('Y-m-d');
        $pengiriman->status = 1;
        $pengiriman->photo = null;

        $data = [
            'title' => 'Duplikasi Pengiriman',
            'pengiriman' => $pengiriman,
            'detail_pengiriman' => $details,
            'pelanggan' => $this->pengirimanService->getCustomersForSelect(),
            'kurir' => $this->pengirimanService->getCouriersForSelect(),
            'barang' => $this->pengirimanService->getItemsForSelect()
        ];

        return view('pengiriman/manage', $data);
    }

    /**
     * Export shipments to Excel
     */
    public function export(): ResponseInterface
    {
        // Get filter parameters
        $filter = [
            'search' => trim($this->request->getGet('search') ?? ''),
            'status' => trim($this->request->getGet('status') ?? ''),
            'tanggal_dari' => trim($this->request->getGet('tanggal_dari') ?? ''),
            'tanggal_sampai' => trim($this->request->getGet('tanggal_sampai') ?? ''),
        ];

        // Get all shipments for export
        [$shipments, $total] = $this->pengirimanService->getAllShipments($filter, 0, 0);

        // Generate Excel file
        $filename = 'pengiriman_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Here you would use a library like PhpSpreadsheet to generate Excel
        // For now, we'll return CSV format
        
        $output = "ID Pengiriman,Tanggal,No PO,Pelanggan,Kurir,No Kendaraan,Penerima,Status\n";
        
        foreach ($shipments as $shipment) {
            $statusText = '';
            switch ($shipment->status) {
                case 1: $statusText = 'Pending'; break;
                case 2: $statusText = 'Dalam Perjalanan'; break;
                case 3: $statusText = 'Terkirim'; break;
                case 4: $statusText = 'Dibatalkan'; break;
            }
            
            $output .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $shipment->id_pengiriman,
                date('d/m/Y', strtotime($shipment->tanggal)),
                $shipment->no_po,
                $shipment->nama_pelanggan ?? '',
                $shipment->nama_kurir ?? '',
                $shipment->no_kendaraan,
                $shipment->penerima,
                $statusText
            );
        }

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($output);
    }
}