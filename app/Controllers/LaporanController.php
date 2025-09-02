<?php

namespace App\Controllers;

use App\Models\PengirimanModel;
use App\Models\PelangganModel;
use App\Models\KurirModel;

class LaporanController extends BaseController
{
    protected $pengirimanModel;
    protected $pelangganModel;
    protected $kurirModel;

    public function __construct()
    {
        $this->pengirimanModel = new PengirimanModel();
        $this->pelangganModel = new PelangganModel();
        $this->kurirModel = new KurirModel();
    }

    public function index()
    {
        // Allow admin (level 1) and kurir (level 2) to access reports
        $userLevel = session()->get('level');
        if (!in_array($userLevel, [1, 2])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses laporan.');
        }

        // Get date range from request
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01'); // First day of current month
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d'); // Today

        // Get shipment data for the date range
        $shipments = $this->pengirimanModel
            ->select('pengiriman.*, pelanggan.nama as nama_pelanggan, kurir.nama as nama_kurir')
            ->join('pelanggan', 'pengiriman.id_pelanggan = pelanggan.id_pelanggan')
            ->join('kurir', 'pengiriman.id_kurir = kurir.id_kurir')
            ->where('pengiriman.tanggal >=', $startDate)
            ->where('pengiriman.tanggal <=', $endDate)
            ->orderBy('pengiriman.tanggal', 'DESC')
            ->findAll();

        // Get summary statistics
        $totalShipments = count($shipments);
        $deliveredShipments = count(array_filter($shipments, function($shipment) {
            return $shipment->status == 1;
        }));
        $pendingShipments = $totalShipments - $deliveredShipments;

        $data = [
            'title' => 'Laporan Pengiriman',
            'active_menu' => 'laporan',
            'shipments' => $shipments,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_shipments' => $totalShipments,
            'delivered_shipments' => $deliveredShipments,
            'pending_shipments' => $pendingShipments
        ];

        return view('laporan/index', $data);
    }

    public function exportExcel()
    {
        // Allow admin (level 1) and kurir (level 2) to export
        $userLevel = session()->get('level');
        if (!in_array($userLevel, [1, 2])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengekspor laporan.');
        }

        // Get date range from request
        $startDate = $this->request->getPost('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getPost('end_date') ?? date('Y-m-d');

        // Get shipment data
        $shipments = $this->pengirimanModel
            ->select('pengiriman.*, pelanggan.nama as nama_pelanggan, kurir.nama as nama_kurir')
            ->join('pelanggan', 'pengiriman.id_pelanggan = pelanggan.id_pelanggan')
            ->join('kurir', 'pengiriman.id_kurir = kurir.id_kurir')
            ->where('pengiriman.tanggal >=', $startDate)
            ->where('pengiriman.tanggal <=', $endDate)
            ->orderBy('pengiriman.tanggal', 'DESC')
            ->findAll();

        // Create Excel file (basic implementation)
        $filename = 'laporan_pengiriman_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Set proper headers for CSV download
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        
        $output = fopen('php://output', 'w');
        
        // Add UTF-8 BOM to fix Excel encoding issues
        fwrite($output, "\xEF\xBB\xBF");
        
        // CSV Header
        fputcsv($output, [
            'ID Pengiriman',
            'Tanggal',
            'Pelanggan',
            'Kurir',
            'No Kendaraan',
            'No PO',
            'Status',
            'Penerima',
            'Keterangan'
        ]);
        
        // CSV Data
        foreach ($shipments as $shipment) {
            $statusText = '';
            switch ($shipment->status) {
                case 1:
                    $statusText = 'Pending';
                    break;
                case 2:
                    $statusText = 'Dalam Perjalanan';
                    break;
                case 3:
                    $statusText = 'Terkirim';
                    break;
                case 4:
                    $statusText = 'Dibatalkan';
                    break;
                default:
                    $statusText = 'Unknown';
            }
            
            fputcsv($output, [
                $shipment->id_pengiriman ?? '',
                $shipment->tanggal ?? '',
                $shipment->nama_pelanggan ?? '',
                $shipment->nama_kurir ?? '',
                $shipment->no_kendaraan ?? '',
                $shipment->no_po ?? '',
                $statusText,
                $shipment->penerima ?? '-',
                $shipment->keterangan ?? '-'
            ]);
        }
        
        fclose($output);
        exit;
    }
}