<?php

namespace App\Controllers;

use App\Models\PengirimanModel;
use App\Models\BarangModel;
use App\Models\PelangganModel;
use App\Models\KurirModel;

class DashboardController extends BaseController
{
    protected PengirimanModel $pengirimanModel;
    protected BarangModel $barangModel;
    protected PelangganModel $pelangganModel;
    protected KurirModel $kurirModel;

    public function __construct()
    {
        $this->pengirimanModel = new PengirimanModel();
        $this->barangModel = new BarangModel();
        $this->pelangganModel = new PelangganModel();
        $this->kurirModel = new KurirModel();
    }

    /**
     * Display dashboard
     */
    public function index(): string
    {
        $userLevel = session()->get('level');

        // This is the robust check I recommended earlier.
        // If for some reason the level is not in the session,
        // the user is not properly logged in.
        if (!$userLevel) {
            return redirect()->to('/login')->with('error', 'Sesi Anda tidak valid. Silakan login kembali.');
        } 
        // Get role name for display
        $roleNames = [1 => 'Admin', 2 => 'Finance', 3 => 'Gudang'];
        $roleName = $roleNames[$userLevel] ?? 'User';
        
        // Set role_name in session for view
        session()->set('role_name', strtolower($roleName));
        
        // Get dashboard statistics based on user role
        $data = [
            'title' => 'Dashboard',
            'meta_description' => 'Logistics Management System Dashboard',
            'userLevel' => $userLevel,
            'roleName' => $roleName,
            'stats' => $this->getDashboardStats($userLevel),
            'recent_activities' => $this->getRecentActivities($userLevel),
            'pageActions' => $this->getPageActions($userLevel),
            'monthlyTrends' => $this->getMonthlyTrends(),
            'statusDistribution' => $this->getStatusDistribution()
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Get dashboard statistics based on user level
     */
    private function getDashboardStats(int $userLevel): array
    {
        $stats = [];

        // Common statistics for all users
        $stats['total_shipments'] = $this->pengirimanModel->countAll();
        $stats['pending_shipments'] = $this->pengirimanModel->where('status', 1)->countAllResults(); // Pending
        $stats['delivered_shipments'] = $this->pengirimanModel->where('status', 3)->countAllResults(); // Delivered  
        $stats['in_transit_shipments'] = $this->pengirimanModel->where('status', 2)->countAllResults(); // In Transit

        // Time-based statistics
        $stats['today_shipments'] = $this->pengirimanModel->where('DATE(tanggal)', date('Y-m-d'))->countAllResults();
        $stats['this_week_shipments'] = $this->pengirimanModel->where('WEEK(tanggal)', date('W'))->where('YEAR(tanggal)', date('Y'))->countAllResults();
        $stats['this_month_shipments'] = $this->pengirimanModel->where('MONTH(tanggal)', date('m'))->where('YEAR(tanggal)', date('Y'))->countAllResults();

        // Previous period comparisons
        $stats['last_month_shipments'] = $this->pengirimanModel
            ->where('MONTH(tanggal)', date('m', strtotime('-1 month')))
            ->where('YEAR(tanggal)', date('Y', strtotime('-1 month')))
            ->countAllResults();

        // Calculate growth percentages
        if ($stats['last_month_shipments'] > 0) {
            $stats['monthly_growth'] = round((($stats['this_month_shipments'] - $stats['last_month_shipments']) / $stats['last_month_shipments']) * 100, 1);
        } else {
            $stats['monthly_growth'] = 0;
        }

        // Additional statistics based on user level
        switch ($userLevel) {
            case USER_LEVEL_ADMIN: // Admin
                // Admin can see all statistics
                $stats['total_items'] = $this->barangModel->countAll();
                $stats['total_customers'] = $this->pelangganModel->countAll();
                $stats['total_couriers'] = $this->kurirModel->countAll();
                
                // Performance metrics
                $stats['delivery_rate'] = $stats['total_shipments'] > 0 ? 
                    round(($stats['delivered_shipments'] / $stats['total_shipments']) * 100, 1) : 0;
                
                // Monthly trends (last 12 months)
                $stats['monthly_trends'] = $this->getMonthlyTrends();
                break;

            case USER_LEVEL_FINANCE: // Finance
                // Finance can see customer and shipment statistics
                $stats['total_customers'] = $this->pelangganModel->countAll();
                $stats['delivery_rate'] = $stats['total_shipments'] > 0 ? 
                    round(($stats['delivered_shipments'] / $stats['total_shipments']) * 100, 1) : 0;
                break;

            case USER_LEVEL_GUDANG: // Gudang
                // Gudang can see item and shipment statistics but NOT customers
                $stats['total_items'] = $this->barangModel->countAll();
                $stats['pending_rate'] = $stats['total_shipments'] > 0 ? 
                    round(($stats['pending_shipments'] / $stats['total_shipments']) * 100, 1) : 0;
                // Explicitly set customers to 0 or hide it
                $stats['total_customers'] = 0; // This will be used to hide the card
                break;
        }

        return $stats;
    }

    /**
     * Get monthly shipment trends for the last 12 months
     */
    private function getMonthlyTrends(): array
    {
        $trends = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-{$i} months"));
            $month = date('M', strtotime("-{$i} months"));
            
            $total = $this->pengirimanModel
                ->where("DATE_FORMAT(tanggal, '%Y-%m')", $date)
                ->countAllResults();
                
            $delivered = $this->pengirimanModel
                ->where("DATE_FORMAT(tanggal, '%Y-%m')", $date)
                ->where('status', SHIPMENT_STATUS_DELIVERED) // Delivered
                ->countAllResults();
            
            $trends[] = [
                'month' => $month,
                'total' => $total,
                'delivered' => $delivered
            ];
        }
        
        return $trends;
    }

    /**
     * Get recent activities based on user level
     */
    private function getRecentActivities(int $userLevel): array
    {
        $activities = [];
        
        // Get recent shipments
        $recentShipments = $this->pengirimanModel
            ->select('pengiriman.*, pelanggan.nama as nama_pelanggan')
            ->join('pelanggan', 'pelanggan.id_pelanggan = pengiriman.id_pelanggan')
            ->orderBy('pengiriman.tanggal', 'DESC')
            ->limit(5)
            ->asArray() 
            ->findAll();

        foreach ($recentShipments as $shipment) {
            $statusText = match($shipment['status']) {
                1 => 'created', // Pending
                2 => 'updated to in transit', // In Transit
                3 => 'delivered', // Delivered
                default => 'updated'
            };
            
            $statusType = match($shipment['status']) {
                1 => 'warning', // Pending
                2 => 'info', // In Transit
                3 => 'success', // Delivered
                default => 'secondary'
            };

            $activities[] = [
                'title' => "Shipment {$shipment['id_pengiriman']} {$statusText}",
                'description' => "Customer: {$shipment['nama_pelanggan']}",
                'time' => $this->timeAgo($shipment['tanggal']),
                'type' => $statusType,
                'icon' => 'fas fa-shipping-fast'
            ];
        }

        return $activities;
    }

    /**
     * Get page actions based on user level
     */
    private function getPageActions(int $userLevel): array
    {
        $actions = [];

        switch ($userLevel) {
            case 1: // Admin
                $actions[] = [
                    'title' => 'System Settings',
                    'url' => base_url('/settings'),
                    'icon' => 'fas fa-cog',
                    'class' => 'btn-outline-primary'
                ];
                break;

            case 2: // Finance
                $actions[] = [
                    'title' => 'Generate Report',
                    'url' => base_url('/laporan'),
                    'icon' => 'fas fa-chart-bar',
                    'class' => 'btn-outline-primary'
                ];
                break;

            case 3: // Gudang
                $actions[] = [
                    'title' => 'New Shipment',
                    'url' => base_url('/pengiriman/create'),
                    'icon' => 'fas fa-plus',
                    'class' => 'btn-primary'
                ];
                break;
        }

        return $actions;
    }

    /**
     * Convert timestamp to human readable time ago format
     */
    private function timeAgo(string $datetime): string
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) {
            return 'Just now';
        } elseif ($time < 3600) {
            $minutes = floor($time / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($time < 86400) {
            $hours = floor($time / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($time < 2592000) {
            $days = floor($time / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', strtotime($datetime));
        }
    }

    /**
     * Get status distribution for dashboard charts
     */
    private function getStatusDistribution(): array
    {
        $distribution = [];
        
        $distribution[] = [
            'label' => 'Pending',
            'value' => $this->pengirimanModel->where('status', 1)->countAllResults(),
            'color' => '#ffc107'
        ];
        
        $distribution[] = [
            'label' => 'In Transit',
            'value' => $this->pengirimanModel->where('status', 2)->countAllResults(),
            'color' => '#17a2b8'
        ];
        
        $distribution[] = [
            'label' => 'Delivered',
            'value' => $this->pengirimanModel->where('status', 3)->countAllResults(),
            'color' => '#28a745'
        ];
        
        $distribution[] = [
            'label' => 'Cancelled',
            'value' => $this->pengirimanModel->where('status', 4)->countAllResults(),
            'color' => '#dc3545'
        ];
        
        return $distribution;
    }
}