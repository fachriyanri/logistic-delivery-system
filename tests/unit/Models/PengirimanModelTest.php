<?php

namespace Tests\Unit\Models;

use App\Models\PengirimanModel;
use App\Entities\PengirimanEntity;
use Tests\Support\DatabaseTestCase;

class PengirimanModelTest extends DatabaseTestCase
{
    private PengirimanModel $pengirimanModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pengirimanModel = new PengirimanModel();
    }

    public function testCanCreatePengiriman(): void
    {
        $pengirimanData = [
            'id_pengiriman' => 'PGR999',
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'PLG001',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B9999XYZ',
            'no_po' => 'PO999',
            'keterangan' => 'Test new shipment',
            'penerima' => 'New Receiver',
            'status' => 1,
        ];

        $result = $this->pengirimanModel->insert($pengirimanData);
        $this->assertTrue($result);

        $pengiriman = $this->pengirimanModel->find('PGR999');
        $this->assertInstanceOf(PengirimanEntity::class, $pengiriman);
        $this->assertEquals('PGR999', $pengiriman->id_pengiriman);
        $this->assertEquals('PLG001', $pengiriman->id_pelanggan);
    }

    public function testCanUpdatePengiriman(): void
    {
        $updateData = [
            'keterangan' => 'Updated shipment description',
            'status' => 2,
        ];

        $result = $this->pengirimanModel->update('PGR001', $updateData);
        $this->assertTrue($result);

        $pengiriman = $this->pengirimanModel->find('PGR001');
        $this->assertEquals('Updated shipment description', $pengiriman->keterangan);
        $this->assertEquals(2, $pengiriman->status);
    }

    public function testCanDeletePengiriman(): void
    {
        $result = $this->pengirimanModel->delete('PGR001');
        $this->assertTrue($result);

        $pengiriman = $this->pengirimanModel->find('PGR001');
        $this->assertNull($pengiriman);
    }

    public function testGetWithDetailsMethod(): void
    {
        $pengiriman = $this->pengirimanModel->getWithDetails('PGR001');
        
        $this->assertInstanceOf(PengirimanEntity::class, $pengiriman);
        $this->assertEquals('PGR001', $pengiriman->id_pengiriman);
        
        // Check if relationships are loaded
        $this->assertObjectHasAttribute('pelanggan', $pengiriman);
        $this->assertObjectHasAttribute('kurir', $pengiriman);
        $this->assertObjectHasAttribute('details', $pengiriman);
    }

    public function testGetShipmentsByDateRangeMethod(): void
    {
        $from = date('Y-m-d', strtotime('-1 day'));
        $to = date('Y-m-d', strtotime('+1 day'));
        
        $shipments = $this->pengirimanModel->getShipmentsByDateRange($from, $to);
        
        $this->assertIsArray($shipments);
        $this->assertCount(1, $shipments);
        $this->assertInstanceOf(PengirimanEntity::class, $shipments[0]);
    }

    public function testGetShipmentsByDateRangeWithFilters(): void
    {
        $from = date('Y-m-d', strtotime('-1 day'));
        $to = date('Y-m-d', strtotime('+1 day'));
        $filters = ['status' => 1];
        
        $shipments = $this->pengirimanModel->getShipmentsByDateRange($from, $to, $filters);
        
        $this->assertIsArray($shipments);
        foreach ($shipments as $shipment) {
            $this->assertEquals(1, $shipment->status);
        }
    }

    public function testGenerateNextIdMethod(): void
    {
        $nextId = $this->pengirimanModel->generateNextId();
        
        $this->assertIsString($nextId);
        $this->assertStringStartsWith('PGR', $nextId);
        $this->assertEquals(6, strlen($nextId));
    }

    public function testGetShipmentsByCustomerMethod(): void
    {
        $shipments = $this->pengirimanModel->getShipmentsByCustomer('PLG001');
        
        $this->assertIsArray($shipments);
        $this->assertCount(1, $shipments);
        $this->assertEquals('PLG001', $shipments[0]->id_pelanggan);
    }

    public function testGetShipmentsByCourierMethod(): void
    {
        $shipments = $this->pengirimanModel->getShipmentsByCourier('KUR001');
        
        $this->assertIsArray($shipments);
        $this->assertCount(1, $shipments);
        $this->assertEquals('KUR001', $shipments[0]->id_kurir);
    }

    public function testGetShipmentsByStatusMethod(): void
    {
        $shipments = $this->pengirimanModel->getShipmentsByStatus(1);
        
        $this->assertIsArray($shipments);
        foreach ($shipments as $shipment) {
            $this->assertEquals(1, $shipment->status);
        }
    }

    public function testValidationRules(): void
    {
        $validationRules = $this->pengirimanModel->getValidationRules();
        
        $this->assertArrayHasKey('id_pengiriman', $validationRules);
        $this->assertArrayHasKey('tanggal', $validationRules);
        $this->assertArrayHasKey('id_pelanggan', $validationRules);
        $this->assertArrayHasKey('id_kurir', $validationRules);
    }

    public function testValidationFailsForInvalidCustomer(): void
    {
        $pengirimanData = [
            'id_pengiriman' => 'PGR999',
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'INVALID',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B9999XYZ',
            'status' => 1,
        ];

        $result = $this->pengirimanModel->insert($pengirimanData);
        $this->assertFalse($result);
        
        $errors = $this->pengirimanModel->errors();
        $this->assertNotEmpty($errors);
    }

    public function testValidationFailsForInvalidCourier(): void
    {
        $pengirimanData = [
            'id_pengiriman' => 'PGR999',
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'PLG001',
            'id_kurir' => 'INVALID',
            'no_kendaraan' => 'B9999XYZ',
            'status' => 1,
        ];

        $result = $this->pengirimanModel->insert($pengirimanData);
        $this->assertFalse($result);
        
        $errors = $this->pengirimanModel->errors();
        $this->assertNotEmpty($errors);
    }

    public function testGetMonthlyShipmentsMethod(): void
    {
        $year = date('Y');
        $month = date('m');
        
        $shipments = $this->pengirimanModel->getMonthlyShipments($year, $month);
        
        $this->assertIsArray($shipments);
        foreach ($shipments as $shipment) {
            $this->assertInstanceOf(PengirimanEntity::class, $shipment);
        }
    }
}