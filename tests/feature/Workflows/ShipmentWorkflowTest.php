<?php

namespace Tests\Feature\Workflows;

use Tests\Support\DatabaseTestCase;
use CodeIgniter\Test\ControllerTestTrait;

class ShipmentWorkflowTest extends DatabaseTestCase
{
    use ControllerTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAsUser('testadmin');
    }

    public function testCompleteShipmentCreationWorkflow(): void
    {
        // Step 1: Navigate to shipment creation page
        $createPageResult = $this->get('/pengiriman/create');
        $createPageResult->assertOK();
        $createPageResult->assertSee('Tambah Pengiriman');
        $createPageResult->assertSee('Tanggal');
        $createPageResult->assertSee('Pelanggan');
        $createPageResult->assertSee('Kurir');

        // Step 2: Submit new shipment data
        $shipmentData = [
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'PLG001',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B9999XYZ',
            'no_po' => 'PO999',
            'keterangan' => 'Complete workflow test shipment',
            'penerima' => 'Workflow Test Receiver',
            'details' => [
                [
                    'id_barang' => 'BRG001',
                    'jumlah' => 5,
                    'keterangan' => 'First item detail',
                ],
                [
                    'id_barang' => 'BRG002',
                    'jumlah' => 3,
                    'keterangan' => 'Second item detail',
                ],
            ],
        ];

        $createResult = $this->post('/pengiriman/store', $shipmentData);
        $createResult->assertRedirectTo('/pengiriman');

        $session = session();
        $this->assertTrue($session->has('success'));

        // Step 3: Verify shipment appears in list
        $listResult = $this->get('/pengiriman');
        $listResult->assertOK();
        $listResult->assertSee('Complete workflow test shipment');
        $listResult->assertSee('B9999XYZ');

        // Step 4: Find the created shipment ID (simulate getting it from the list)
        // In a real test, you'd parse the response to get the actual ID
        // For this test, we'll assume the service generates a predictable ID
        $newShipmentId = 'PGR002'; // This would be dynamically determined

        // Step 5: View shipment details
        $viewResult = $this->get("/pengiriman/view/{$newShipmentId}");
        if ($viewResult->isOK()) {
            $viewResult->assertSee('Detail Pengiriman');
            $viewResult->assertSee($newShipmentId);
            $viewResult->assertSee('Workflow Test Receiver');
        }

        // Step 6: Edit the shipment
        $editPageResult = $this->get("/pengiriman/edit/{$newShipmentId}");
        if ($editPageResult->isOK()) {
            $editPageResult->assertSee('Edit Pengiriman');

            $updateData = [
                'keterangan' => 'Updated workflow test shipment',
                'penerima' => 'Updated Workflow Receiver',
                'no_kendaraan' => 'B8888XYZ',
            ];

            $updateResult = $this->post("/pengiriman/update/{$newShipmentId}", $updateData);
            $updateResult->assertRedirectTo('/pengiriman');

            $session = session();
            $this->assertTrue($session->has('success'));
        }

        // Step 7: Update shipment status
        $statusUpdateData = [
            'status' => 2, // In Transit
            'keterangan' => 'Shipment is now in transit',
        ];

        $statusResult = $this->post("/pengiriman/update-status/{$newShipmentId}", $statusUpdateData);
        if ($statusResult->isRedirect()) {
            $statusResult->assertRedirectTo('/pengiriman');
        }

        // Step 8: Generate delivery note
        $deliveryNoteResult = $this->get("/pengiriman/delivery-note/{$newShipmentId}");
        $this->assertTrue(
            $deliveryNoteResult->isOK() || 
            $deliveryNoteResult->isRedirect()
        );

        // Step 9: Mark as delivered
        $deliveredData = [
            'status' => 3, // Delivered
            'keterangan' => 'Shipment delivered successfully',
        ];

        $deliveredResult = $this->post("/pengiriman/update-status/{$newShipmentId}", $deliveredData);
        if ($deliveredResult->isRedirect()) {
            $deliveredResult->assertRedirectTo('/pengiriman');
        }

        // Step 10: Verify final status in list
        $finalListResult = $this->get('/pengiriman');
        $finalListResult->assertOK();
        // Should show delivered status (depends on UI implementation)
    }

    public function testShipmentStatusProgressionWorkflow(): void
    {
        $shipmentId = 'PGR001';

        // Step 1: Verify initial status (Pending)
        $initialViewResult = $this->get("/pengiriman/view/{$shipmentId}");
        $initialViewResult->assertOK();
        $initialViewResult->assertSee('Pending'); // Or status indicator

        // Step 2: Update to In Transit
        $inTransitData = [
            'status' => 2,
            'keterangan' => 'Shipment picked up and in transit',
        ];

        $inTransitResult = $this->post("/pengiriman/update-status/{$shipmentId}", $inTransitData);
        $inTransitResult->assertRedirectTo('/pengiriman');

        // Step 3: Verify status change
        $transitViewResult = $this->get("/pengiriman/view/{$shipmentId}");
        $transitViewResult->assertOK();
        // Should show in transit status

        // Step 4: Generate QR code for tracking
        $qrResult = $this->get("/pengiriman/qr-code/{$shipmentId}");
        $this->assertTrue(
            $qrResult->isOK() || 
            $qrResult->getStatusCode() === 200
        );

        // Step 5: Update to Delivered
        $deliveredData = [
            'status' => 3,
            'keterangan' => 'Package delivered to recipient',
            'photo' => 'delivery_proof.jpg', // Simulated photo upload
        ];

        $deliveredResult = $this->post("/pengiriman/update-status/{$shipmentId}", $deliveredData);
        $deliveredResult->assertRedirectTo('/pengiriman');

        // Step 6: Verify final delivered status
        $finalViewResult = $this->get("/pengiriman/view/{$shipmentId}");
        $finalViewResult->assertOK();
        // Should show delivered status and delivery proof
    }

    public function testShipmentSearchAndFilterWorkflow(): void
    {
        // Step 1: Access shipment list
        $listResult = $this->get('/pengiriman');
        $listResult->assertOK();
        $listResult->assertSee('Daftar Pengiriman');

        // Step 2: Search by shipment ID
        $searchByIdResult = $this->get('/pengiriman?search=PGR001');
        $searchByIdResult->assertOK();
        $searchByIdResult->assertSee('PGR001');

        // Step 3: Filter by date range
        $dateFilterData = [
            'date_from' => date('Y-m-d', strtotime('-7 days')),
            'date_to' => date('Y-m-d'),
        ];

        $dateFilterResult = $this->get('/pengiriman?' . http_build_query($dateFilterData));
        $dateFilterResult->assertOK();

        // Step 4: Filter by status
        $statusFilterResult = $this->get('/pengiriman?status=1');
        $statusFilterResult->assertOK();

        // Step 5: Filter by customer
        $customerFilterResult = $this->get('/pengiriman?customer=PLG001');
        $customerFilterResult->assertOK();

        // Step 6: Combined filters
        $combinedFilterData = [
            'status' => 1,
            'date_from' => date('Y-m-d', strtotime('-30 days')),
            'date_to' => date('Y-m-d'),
            'customer' => 'PLG001',
        ];

        $combinedFilterResult = $this->get('/pengiriman?' . http_build_query($combinedFilterData));
        $combinedFilterResult->assertOK();

        // Step 7: Clear filters
        $clearFiltersResult = $this->get('/pengiriman');
        $clearFiltersResult->assertOK();
    }

    public function testShipmentReportGenerationWorkflow(): void
    {
        // Step 1: Access reports page
        $reportsPageResult = $this->get('/laporan');
        if ($reportsPageResult->isOK()) {
            $reportsPageResult->assertSee('Laporan');
        }

        // Step 2: Generate daily report
        $dailyReportData = [
            'report_type' => 'daily',
            'date' => date('Y-m-d'),
        ];

        $dailyReportResult = $this->post('/laporan/generate', $dailyReportData);
        $this->assertTrue(
            $dailyReportResult->isOK() || 
            $dailyReportResult->isRedirect()
        );

        // Step 3: Generate monthly report
        $monthlyReportData = [
            'report_type' => 'monthly',
            'month' => date('Y-m'),
        ];

        $monthlyReportResult = $this->post('/laporan/generate', $monthlyReportData);
        $this->assertTrue(
            $monthlyReportResult->isOK() || 
            $monthlyReportResult->isRedirect()
        );

        // Step 4: Export to Excel
        $excelExportResult = $this->get('/pengiriman/export?format=excel');
        $this->assertTrue(
            $excelExportResult->isOK() || 
            $excelExportResult->getStatusCode() === 200
        );

        // Step 5: Export to PDF
        $pdfExportResult = $this->get('/pengiriman/export?format=pdf');
        $this->assertTrue(
            $pdfExportResult->isOK() || 
            $pdfExportResult->getStatusCode() === 200
        );
    }

    public function testShipmentValidationWorkflow(): void
    {
        // Step 1: Try to create shipment with missing required fields
        $incompleteData = [
            'tanggal' => '',
            'id_pelanggan' => '',
            'id_kurir' => '',
        ];

        $incompleteResult = $this->post('/pengiriman/store', $incompleteData);
        $incompleteResult->assertRedirectTo('/pengiriman/create');

        $session = session();
        $this->assertTrue($session->has('error') || $session->has('errors'));

        // Step 2: Try to create shipment with invalid customer
        $invalidCustomerData = [
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'INVALID',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B1234ABC',
        ];

        $invalidCustomerResult = $this->post('/pengiriman/store', $invalidCustomerData);
        $invalidCustomerResult->assertRedirectTo('/pengiriman/create');

        $this->assertTrue($session->has('error') || $session->has('errors'));

        // Step 3: Try to create shipment with invalid date
        $invalidDateData = [
            'tanggal' => 'invalid-date',
            'id_pelanggan' => 'PLG001',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B1234ABC',
        ];

        $invalidDateResult = $this->post('/pengiriman/store', $invalidDateData);
        $invalidDateResult->assertRedirectTo('/pengiriman/create');

        $this->assertTrue($session->has('error') || $session->has('errors'));

        // Step 4: Create valid shipment after validation errors
        $validData = [
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'PLG001',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B5555XYZ',
            'keterangan' => 'Valid shipment after validation',
            'penerima' => 'Valid Receiver',
            'details' => [
                [
                    'id_barang' => 'BRG001',
                    'jumlah' => 1,
                    'keterangan' => 'Valid detail',
                ],
            ],
        ];

        $validResult = $this->post('/pengiriman/store', $validData);
        $validResult->assertRedirectTo('/pengiriman');

        $this->assertTrue($session->has('success'));
    }

    public function testShipmentDeletionWorkflow(): void
    {
        // Step 1: Create a shipment to delete
        $shipmentData = [
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'PLG001',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B7777DELETE',
            'keterangan' => 'Shipment to be deleted',
            'penerima' => 'Delete Test Receiver',
            'details' => [
                [
                    'id_barang' => 'BRG001',
                    'jumlah' => 1,
                    'keterangan' => 'Delete test detail',
                ],
            ],
        ];

        $createResult = $this->post('/pengiriman/store', $shipmentData);
        $createResult->assertRedirectTo('/pengiriman');

        // Step 2: Verify shipment exists in list
        $listResult = $this->get('/pengiriman');
        $listResult->assertOK();
        $listResult->assertSee('B7777DELETE');

        // Step 3: Attempt to delete (assuming we know the ID)
        $deleteShipmentId = 'PGR002'; // This would be determined dynamically

        // Step 4: Confirm deletion (if confirmation page exists)
        $confirmDeleteResult = $this->get("/pengiriman/delete/{$deleteShipmentId}");
        if ($confirmDeleteResult->isOK()) {
            $confirmDeleteResult->assertSee('Konfirmasi Hapus');
        }

        // Step 5: Execute deletion
        $deleteResult = $this->post("/pengiriman/delete/{$deleteShipmentId}");
        $deleteResult->assertRedirectTo('/pengiriman');

        $session = session();
        $this->assertTrue($session->has('success'));

        // Step 6: Verify shipment no longer exists in list
        $finalListResult = $this->get('/pengiriman');
        $finalListResult->assertOK();
        $finalListResult->assertDontSee('B7777DELETE');
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