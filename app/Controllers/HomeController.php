<?php

namespace App\Controllers;

use App\Services\PengirimanService;

/**
 * Home Controller
 * 
 * Handles public homepage and tracking functionality
 */
class HomeController extends BaseController
{
    protected PengirimanService $pengirimanService;

    public function __construct()
    {
        $this->pengirimanService = new PengirimanService();
    }

    /**
     * Display homepage
     */
    public function index(): string
    {
        $data = [
            'title' => 'Puninar Yusen Logistics Indonesia'
        ];

        return view('home/index', $data);
    }

    /**
     * Display tracking page
     */
    public function track(): string
    {
        // Get filter parameters
        $search = trim($this->request->getGet('search') ?? '');
        $status = trim($this->request->getGet('status') ?? '');
        $tanggal_dari = trim($this->request->getGet('tanggal_dari') ?? '');
        $tanggal_sampai = trim($this->request->getGet('tanggal_sampai') ?? '');

        $pengiriman = [];
        $total = 0;
        $pager = null;
        $currentPage = 1;
        $perPage = 15;

        // Only search if there's a search term (ID, No PO, or Penerima)
        if (!empty($search)) {
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

            $currentPage = (int) ($this->request->getGet('page') ?? 1);
            $offset = ($currentPage - 1) * $perPage;

            // Get shipments data
            [$pengiriman, $total] = $this->pengirimanService->getAllShipments($filter, $perPage, $offset);
            
            // Setup pagination
            $pager = \Config\Services::pager();
            $pager->setPath('track');
            $pager->makeLinks($currentPage, $perPage, $total);
        }

        $data = [
            'title' => 'Track Shipment - Puninar Yusen Logistics Indonesia',
            'pengiriman' => $pengiriman,
            'total' => $total,
            'search' => $search,
            'status' => $status,
            'tanggal_dari' => $tanggal_dari,
            'tanggal_sampai' => $tanggal_sampai,
            'pager' => $pager,
            'currentPage' => $currentPage,
            'perPage' => $perPage
        ];

        return view('home/track', $data);
    }

    /**
     * Display shipment detail for public users
     */
    public function trackDetail(?string $id = null): string
    {
        if (empty($id)) {
            session()->setFlashdata('error', 'ID pengiriman tidak valid');
            return redirect()->to('/track');
        }

        $pengiriman = $this->pengirimanService->getShipmentById($id);
        if (!$pengiriman) {
            session()->setFlashdata('error', 'Pengiriman tidak ditemukan');
            return redirect()->to('/track');
        }

        $details = $this->pengirimanService->getShipmentDetails($id);

        $data = [
            'title' => 'Detail Pengiriman - ' . $pengiriman->id_pengiriman,
            'pengiriman' => $pengiriman,
            'detail_pengiriman' => $details
        ];

        return view('home/track_detail', $data);
    }
}