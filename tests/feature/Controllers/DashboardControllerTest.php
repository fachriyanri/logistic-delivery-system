<?php

namespace Tests\Feature\Controllers;

use Tests\Support\DatabaseTestCase;
use CodeIgniter\Test\ControllerTestTrait;

class DashboardControllerTest extends DatabaseTestCase
{
    use ControllerTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testDashboardRequiresAuthentication(): void
    {
        $result = $this->get('/dashboard');
        $result->assertRedirectTo('/auth/login');
    }

    public function testDashboardDisplaysForAuthenticatedAdmin(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/dashboard');
        
        $result->assertOK();
        $result->assertSee('Dashboard');
        $result->assertSee('Total Pengiriman');
        $result->assertSee('Pengiriman Pending');
        $result->assertSee('Pengiriman Selesai');
    }

    public function testDashboardDisplaysForAuthenticatedFinance(): void
    {
        $this->loginAsUser('testfinance');
        
        $result = $this->get('/dashboard');
        
        $result->assertOK();
        $result->assertSee('Dashboard');
        // Finance user should see relevant statistics
        $result->assertSee('Total Pengiriman');
    }

    public function testDashboardDisplaysForAuthenticatedGudang(): void
    {
        $this->loginAsUser('testgudang');
        
        $result = $this->get('/dashboard');
        
        $result->assertOK();
        $result->assertSee('Dashboard');
        // Gudang user should see relevant statistics
        $result->assertSee('Total Pengiriman');
    }

    public function testDashboardShowsCorrectStatistics(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/dashboard');
        
        $result->assertOK();
        
        // Should display statistics cards
        $result->assertSee('Total Pengiriman');
        $result->assertSee('Pengiriman Pending');
        $result->assertSee('Pengiriman Dalam Perjalanan');
        $result->assertSee('Pengiriman Selesai');
        
        // Should show actual numbers (at least 1 from test data)
        $result->assertSeeInOrder(['1', 'Total']); // At least 1 shipment
    }

    public function testDashboardShowsRecentShipments(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/dashboard');
        
        $result->assertOK();
        $result->assertSee('Pengiriman Terbaru');
        $result->assertSee('PGR001'); // Should see test shipment
    }

    public function testDashboardShowsChartData(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/dashboard');
        
        $result->assertOK();
        
        // Should include chart containers or data
        $result->assertSee('chart'); // Chart container or script
    }

    public function testDashboardAPIEndpointForStatistics(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/dashboard/api/statistics');
        
        $result->assertOK();
        $result->assertJSONFragment([
            'total_shipments' => 1,
        ]);
    }

    public function testDashboardAPIEndpointForChartData(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/dashboard/api/chart-data');
        
        $result->assertOK();
        
        $json = $result->getJSON();
        $this->assertIsArray($json);
        $this->assertArrayHasKey('monthly_data', $json);
    }

    public function testDashboardAPIRequiresAuthentication(): void
    {
        $result = $this->get('/dashboard/api/statistics');
        $result->assertRedirectTo('/auth/login');
    }

    public function testDashboardShowsUserSpecificContent(): void
    {
        // Test admin user sees admin-specific content
        $this->loginAsUser('testadmin');
        $adminResult = $this->get('/dashboard');
        $adminResult->assertOK();
        $adminResult->assertSee('Admin Dashboard'); // Admin-specific content
        
        // Logout and login as finance user
        $this->get('/auth/logout');
        $this->loginAsUser('testfinance');
        $financeResult = $this->get('/dashboard');
        $financeResult->assertOK();
        // Finance might see different content or same content with different permissions
        $financeResult->assertSee('Dashboard');
    }

    public function testDashboardHandlesEmptyData(): void
    {
        // Clear all shipment data
        $this->db->table('detail_pengiriman')->truncate();
        $this->db->table('pengiriman')->truncate();
        
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/dashboard');
        
        $result->assertOK();
        $result->assertSee('0'); // Should show 0 for empty statistics
        $result->assertSee('Belum ada data'); // Or similar empty state message
    }

    public function testDashboardPerformanceWithLargeDataset(): void
    {
        // Create multiple test shipments
        $shipmentData = [];
        for ($i = 1; $i <= 50; $i++) {
            $shipmentData[] = [
                'id_pengiriman' => 'PGR' . str_pad($i + 100, 3, '0', STR_PAD_LEFT),
                'tanggal' => date('Y-m-d', strtotime("-{$i} days")),
                'id_pelanggan' => 'PLG001',
                'id_kurir' => 'KUR001',
                'no_kendaraan' => 'B' . $i . 'ABC',
                'status' => rand(1, 4),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        $this->db->table('pengiriman')->insertBatch($shipmentData);
        
        $this->loginAsUser('testadmin');
        
        $startTime = microtime(true);
        $result = $this->get('/dashboard');
        $endTime = microtime(true);
        
        $result->assertOK();
        
        // Dashboard should load within reasonable time (2 seconds)
        $loadTime = $endTime - $startTime;
        $this->assertLessThan(2.0, $loadTime, 'Dashboard should load within 2 seconds');
    }

    public function testDashboardRefreshFunctionality(): void
    {
        $this->loginAsUser('testadmin');
        
        // Test AJAX refresh endpoint
        $result = $this->get('/dashboard/refresh', [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ]);
        
        $result->assertOK();
        
        $json = $result->getJSON();
        $this->assertIsArray($json);
        $this->assertArrayHasKey('statistics', $json);
    }

    public function testDashboardDateRangeFilter(): void
    {
        $this->loginAsUser('testadmin');
        
        $data = [
            'date_from' => date('Y-m-d', strtotime('-30 days')),
            'date_to' => date('Y-m-d'),
        ];
        
        $result = $this->get('/dashboard?' . http_build_query($data));
        
        $result->assertOK();
        $result->assertSee('Dashboard');
    }

    /**
     * Helper method to login as a specific user
     */
    private function loginAsUser(string $username): void
    {
        $data = [
            'username' => $username,
            'password' => 'testpass123',
        ];
        
        $this->post('/auth/authenticate', $data);
    }
}