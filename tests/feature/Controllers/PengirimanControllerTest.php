<?php

namespace Tests\Feature\Controllers;

use Tests\Support\DatabaseTestCase;
use CodeIgniter\Test\ControllerTestTrait;

class PengirimanControllerTest extends DatabaseTestCase
{
    use ControllerTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testIndexRequiresAuthentication(): void
    {
        $result = $this->get('/pengiriman');
        $result->assertRedirectTo('/auth/login');
    }

    public function testIndexDisplaysShipmentsForAuthenticatedUser(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/pengiriman');
        
        $result->assertOK();
        $result->assertSee('Daftar Pengiriman');
        $result->assertSee('PGR001'); // Should see test shipment
    }

    public function testCreatePageRequiresAuthentication(): void
    {
        $result = $this->get('/pengiriman/create');
        $result->assertRedirectTo('/auth/login');
    }

    public function testCreatePageDisplaysFormForAuthenticatedUser(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/pengiriman/create');
        
        $result->assertOK();
        $result->assertSee('Tambah Pengiriman');
        $result->assertSee('Tanggal');
        $result->assertSee('Pelanggan');
        $result->assertSee('Kurir');
    }

    public function testStoreCreatesNewShipment(): void
    {
        $this->loginAsUser('testadmin');
        
        $data = [
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'PLG001',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B9999XYZ',
            'no_po' => 'PO999',
            'keterangan' => 'Test shipment from controller',
            'penerima' => 'Controller Test Receiver',
            'details' => [
                [
                    'id_barang' => 'BRG001',
                    'jumlah' => 5,
                    'keterangan' => 'Test detail',
                ],
            ],
        ];

        $result = $this->post('/pengiriman/store', $data);

        $result->assertRedirectTo('/pengiriman');
        
        $session = session();
        $this->assertTrue($session->has('success'));
    }

    public function testStoreValidatesRequiredFields(): void
    {
        $this->loginAsUser('testadmin');
        
        $data = [
            'tanggal' => '',
            'id_pelanggan' => '',
            'id_kurir' => '',
        ];

        $result = $this->post('/pengiriman/store', $data);

        $result->assertRedirectTo('/pengiriman/create');
        
        $session = session();
        $this->assertTrue($session->has('error') || $session->has('errors'));
    }

    public function testEditPageRequiresAuthentication(): void
    {
        $result = $this->get('/pengiriman/edit/PGR001');
        $result->assertRedirectTo('/auth/login');
    }

    public function testEditPageDisplaysShipmentData(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/pengiriman/edit/PGR001');
        
        $result->assertOK();
        $result->assertSee('Edit Pengiriman');
        $result->assertSee('PGR001');
        $result->assertSee('PLG001');
    }

    public function testEditPageReturns404ForNonExistentShipment(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/pengiriman/edit/NONEXISTENT');
        $result->assertStatus(404);
    }

    public function testUpdateModifiesExistingShipment(): void
    {
        $this->loginAsUser('testadmin');
        
        $data = [
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'PLG002',
            'id_kurir' => 'KUR002',
            'no_kendaraan' => 'B8888XYZ',
            'keterangan' => 'Updated shipment description',
            'penerima' => 'Updated Receiver',
        ];

        $result = $this->post('/pengiriman/update/PGR001', $data);

        $result->assertRedirectTo('/pengiriman');
        
        $session = session();
        $this->assertTrue($session->has('success'));
    }

    public function testDeleteRemovesShipment(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->post('/pengiriman/delete/PGR001');

        $result->assertRedirectTo('/pengiriman');
        
        $session = session();
        $this->assertTrue($session->has('success'));
    }

    public function testDeleteReturns404ForNonExistentShipment(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->post('/pengiriman/delete/NONEXISTENT');
        $result->assertStatus(404);
    }

    public function testViewDisplaysShipmentDetails(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/pengiriman/view/PGR001');
        
        $result->assertOK();
        $result->assertSee('Detail Pengiriman');
        $result->assertSee('PGR001');
        $result->assertSee('Test Receiver 1');
    }

    public function testGenerateDeliveryNoteCreatesDocument(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/pengiriman/delivery-note/PGR001');
        
        // Should return a PDF or redirect with success
        $this->assertTrue($result->isOK() || $result->isRedirect());
        
        if ($result->isRedirect()) {
            $session = session();
            $this->assertTrue($session->has('success'));
        }
    }

    public function testUpdateStatusChangesShipmentStatus(): void
    {
        $this->loginAsUser('testadmin');
        
        $data = [
            'status' => 2,
            'keterangan' => 'Status updated to in transit',
        ];

        $result = $this->post('/pengiriman/update-status/PGR001', $data);

        $result->assertRedirectTo('/pengiriman');
        
        $session = session();
        $this->assertTrue($session->has('success'));
    }

    public function testRoleBasedAccessForKurirUser(): void
    {
        $this->loginAsUser('testfinance');
        
        // Kurir should be able to view shipments
        $result = $this->get('/pengiriman');
        $result->assertOK();
        
        // But might not be able to create new ones (depending on business rules)
        $createResult = $this->get('/pengiriman/create');
        // This depends on your role configuration
        $this->assertTrue($createResult->isOK() || $createResult->getStatusCode() === 403);
    }

    public function testRoleBasedAccessForGudangUser(): void
    {
        $this->loginAsUser('testgudang');
        
        // Gudang should be able to view and manage shipments
        $result = $this->get('/pengiriman');
        $result->assertOK();
        
        $createResult = $this->get('/pengiriman/create');
        $createResult->assertOK();
    }

    public function testSearchFunctionality(): void
    {
        $this->loginAsUser('testadmin');
        
        $data = [
            'search' => 'PGR001',
        ];

        $result = $this->get('/pengiriman?' . http_build_query($data));
        
        $result->assertOK();
        $result->assertSee('PGR001');
    }

    public function testFilterByDateRange(): void
    {
        $this->loginAsUser('testadmin');
        
        $data = [
            'date_from' => date('Y-m-d', strtotime('-1 day')),
            'date_to' => date('Y-m-d', strtotime('+1 day')),
        ];

        $result = $this->get('/pengiriman?' . http_build_query($data));
        
        $result->assertOK();
        $result->assertSee('PGR001');
    }

    public function testFilterByStatus(): void
    {
        $this->loginAsUser('testadmin');
        
        $data = [
            'status' => 1,
        ];

        $result = $this->get('/pengiriman?' . http_build_query($data));
        
        $result->assertOK();
        $result->assertSee('PGR001');
    }

    public function testExportFunctionality(): void
    {
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/pengiriman/export');
        
        // Should return a file download or redirect with success
        $this->assertTrue(
            $result->isOK() || 
            $result->isRedirect() ||
            $result->getStatusCode() === 200
        );
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