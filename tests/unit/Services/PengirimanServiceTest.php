<?php

namespace Tests\Unit\Services;

use App\Services\PengirimanService;
use App\Models\PengirimanModel;
use App\Models\DetailPengirimanModel;
use App\Services\QRCodeService;
use App\Entities\PengirimanEntity;
use Tests\Support\DatabaseTestCase;

class PengirimanServiceTest extends DatabaseTestCase
{
    private PengirimanService $pengirimanService;
    private PengirimanModel $pengirimanModel;
    private DetailPengirimanModel $detailModel;
    private QRCodeService $qrCodeService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->pengirimanModel = new PengirimanModel();
        $this->detailModel = new DetailPengirimanModel();
        $this->qrCodeService = new QRCodeService();
        
        $this->pengirimanService = new PengirimanService(
            $this->pengirimanModel,
            $this->detailModel,
            $this->qrCodeService
        );
    }

    public function testCreateShipmentWithValidData(): void
    {
        $shipmentData = [
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'PLG001',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B9999XYZ',
            'no_po' => 'PO999',
            'keterangan' => 'Test shipment from service',
            'penerima' => 'Service Test Receiver',
            'details' => [
                [
                    'id_barang' => 'BRG001',
                    'jumlah' => 5,
                    'keterangan' => 'Test detail 1',
                ],
                [
                    'id_barang' => 'BRG002',
                    'jumlah' => 3,
                    'keterangan' => 'Test detail 2',
                ],
            ],
        ];

        $result = $this->pengirimanService->createShipment($shipmentData);

        $this->assertInstanceOf(PengirimanEntity::class, $result);
        $this->assertEquals('PLG001', $result->id_pelanggan);
        $this->assertEquals('KUR001', $result->id_kurir);
        $this->assertEquals(1, $result->status); // Default status should be pending
    }

    public function testCreateShipmentGeneratesUniqueId(): void
    {
        $shipmentData = [
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'PLG001',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B9999XYZ',
            'details' => [
                [
                    'id_barang' => 'BRG001',
                    'jumlah' => 1,
                    'keterangan' => 'Test',
                ],
            ],
        ];

        $result1 = $this->pengirimanService->createShipment($shipmentData);
        $result2 = $this->pengirimanService->createShipment($shipmentData);

        $this->assertNotEquals($result1->id_pengiriman, $result2->id_pengiriman);
    }

    public function testUpdateShipmentStatus(): void
    {
        $result = $this->pengirimanService->updateShipmentStatus('PGR001', 2, [
            'keterangan' => 'Updated via service',
        ]);

        $this->assertTrue($result);

        $shipment = $this->pengirimanModel->find('PGR001');
        $this->assertEquals(2, $shipment->status);
        $this->assertEquals('Updated via service', $shipment->keterangan);
    }

    public function testUpdateShipmentStatusWithInvalidId(): void
    {
        $result = $this->pengirimanService->updateShipmentStatus('INVALID', 2);
        $this->assertFalse($result);
    }

    public function testGenerateShipmentReport(): void
    {
        $filters = [
            'date_from' => date('Y-m-d', strtotime('-1 day')),
            'date_to' => date('Y-m-d', strtotime('+1 day')),
        ];

        $report = $this->pengirimanService->generateShipmentReport($filters);

        $this->assertIsArray($report);
        $this->assertArrayHasKey('shipments', $report);
        $this->assertArrayHasKey('summary', $report);
        $this->assertArrayHasKey('total_shipments', $report['summary']);
        $this->assertArrayHasKey('status_breakdown', $report['summary']);
    }

    public function testGenerateShipmentReportWithStatusFilter(): void
    {
        $filters = [
            'date_from' => date('Y-m-d', strtotime('-1 day')),
            'date_to' => date('Y-m-d', strtotime('+1 day')),
            'status' => 1,
        ];

        $report = $this->pengirimanService->generateShipmentReport($filters);

        $this->assertIsArray($report);
        foreach ($report['shipments'] as $shipment) {
            $this->assertEquals(1, $shipment->status);
        }
    }

    public function testGetShipmentsByDateRange(): void
    {
        $from = date('Y-m-d', strtotime('-1 day'));
        $to = date('Y-m-d', strtotime('+1 day'));

        $shipments = $this->pengirimanService->getShipmentsByDateRange($from, $to);

        $this->assertIsArray($shipments);
        $this->assertCount(1, $shipments);
        $this->assertInstanceOf(PengirimanEntity::class, $shipments[0]);
    }

    public function testGetShipmentDetails(): void
    {
        $shipment = $this->pengirimanService->getShipmentDetails('PGR001');

        $this->assertInstanceOf(PengirimanEntity::class, $shipment);
        $this->assertEquals('PGR001', $shipment->id_pengiriman);
        
        // Should include related data
        $this->assertObjectHasAttribute('pelanggan', $shipment);
        $this->assertObjectHasAttribute('kurir', $shipment);
        $this->assertObjectHasAttribute('details', $shipment);
    }

    public function testGetShipmentDetailsWithInvalidId(): void
    {
        $shipment = $this->pengirimanService->getShipmentDetails('INVALID');
        $this->assertNull($shipment);
    }

    public function testGenerateDeliveryNote(): void
    {
        $deliveryNote = $this->pengirimanService->generateDeliveryNote('PGR001');

        $this->assertIsArray($deliveryNote);
        $this->assertArrayHasKey('shipment', $deliveryNote);
        $this->assertArrayHasKey('qr_code', $deliveryNote);
        $this->assertArrayHasKey('details', $deliveryNote);
        
        $this->assertInstanceOf(PengirimanEntity::class, $deliveryNote['shipment']);
        $this->assertIsString($deliveryNote['qr_code']);
        $this->assertIsArray($deliveryNote['details']);
    }

    public function testValidateShipmentData(): void
    {
        $validData = [
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'PLG001',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B1234ABC',
            'details' => [
                [
                    'id_barang' => 'BRG001',
                    'jumlah' => 1,
                ],
            ],
        ];

        $result = $this->pengirimanService->validateShipmentData($validData);
        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    public function testValidateShipmentDataWithInvalidData(): void
    {
        $invalidData = [
            'tanggal' => 'invalid-date',
            'id_pelanggan' => '',
            'id_kurir' => '',
            'details' => [],
        ];

        $result = $this->pengirimanService->validateShipmentData($invalidData);
        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['errors']);
    }

    public function testGetShipmentStatistics(): void
    {
        $stats = $this->pengirimanService->getShipmentStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_shipments', $stats);
        $this->assertArrayHasKey('pending_shipments', $stats);
        $this->assertArrayHasKey('in_transit_shipments', $stats);
        $this->assertArrayHasKey('delivered_shipments', $stats);
        $this->assertArrayHasKey('cancelled_shipments', $stats);
    }

    public function testGetMonthlyShipmentTrend(): void
    {
        $trend = $this->pengirimanService->getMonthlyShipmentTrend(2024);

        $this->assertIsArray($trend);
        $this->assertCount(12, $trend); // Should have data for all 12 months
        
        foreach ($trend as $month => $data) {
            $this->assertArrayHasKey('month', $data);
            $this->assertArrayHasKey('count', $data);
            $this->assertIsInt($data['count']);
        }
    }
}