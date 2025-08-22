<?php

namespace Tests\Unit\Entities;

use App\Entities\PengirimanEntity;
use CodeIgniter\Test\CIUnitTestCase;

class PengirimanEntityTest extends CIUnitTestCase
{
    private PengirimanEntity $pengirimanEntity;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pengirimanEntity = new PengirimanEntity();
    }

    public function testCanSetAndGetBasicProperties(): void
    {
        $data = [
            'id_pengiriman' => 'PGR001',
            'tanggal' => '2024-01-15',
            'id_pelanggan' => 'PLG001',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B1234ABC',
            'no_po' => 'PO001',
            'keterangan' => 'Test shipment',
            'penerima' => 'Test Receiver',
            'status' => 1,
        ];

        $this->pengirimanEntity->fill($data);

        $this->assertEquals('PGR001', $this->pengirimanEntity->id_pengiriman);
        $this->assertEquals('PLG001', $this->pengirimanEntity->id_pelanggan);
        $this->assertEquals('KUR001', $this->pengirimanEntity->id_kurir);
        $this->assertEquals('B1234ABC', $this->pengirimanEntity->no_kendaraan);
        $this->assertEquals('PO001', $this->pengirimanEntity->no_po);
        $this->assertEquals('Test shipment', $this->pengirimanEntity->keterangan);
        $this->assertEquals('Test Receiver', $this->pengirimanEntity->penerima);
        $this->assertEquals(1, $this->pengirimanEntity->status);
    }

    public function testGetStatusTextMethod(): void
    {
        $this->pengirimanEntity->status = 1;
        $this->assertEquals('Pending', $this->pengirimanEntity->getStatusText());

        $this->pengirimanEntity->status = 2;
        $this->assertEquals('In Transit', $this->pengirimanEntity->getStatusText());

        $this->pengirimanEntity->status = 3;
        $this->assertEquals('Delivered', $this->pengirimanEntity->getStatusText());

        $this->pengirimanEntity->status = 4;
        $this->assertEquals('Cancelled', $this->pengirimanEntity->getStatusText());

        $this->pengirimanEntity->status = 99;
        $this->assertEquals('Unknown', $this->pengirimanEntity->getStatusText());
    }

    public function testIsDeliveredMethod(): void
    {
        $this->pengirimanEntity->status = 1;
        $this->assertFalse($this->pengirimanEntity->isDelivered());

        $this->pengirimanEntity->status = 2;
        $this->assertFalse($this->pengirimanEntity->isDelivered());

        $this->pengirimanEntity->status = 3;
        $this->assertTrue($this->pengirimanEntity->isDelivered());

        $this->pengirimanEntity->status = 4;
        $this->assertFalse($this->pengirimanEntity->isDelivered());
    }

    public function testCanBeModifiedMethod(): void
    {
        $this->pengirimanEntity->status = 1;
        $this->assertTrue($this->pengirimanEntity->canBeModified());

        $this->pengirimanEntity->status = 2;
        $this->assertTrue($this->pengirimanEntity->canBeModified());

        $this->pengirimanEntity->status = 3;
        $this->assertFalse($this->pengirimanEntity->canBeModified());

        $this->pengirimanEntity->status = 4;
        $this->assertFalse($this->pengirimanEntity->canBeModified());
    }

    public function testIsPendingMethod(): void
    {
        $this->pengirimanEntity->status = 1;
        $this->assertTrue($this->pengirimanEntity->isPending());

        $this->pengirimanEntity->status = 2;
        $this->assertFalse($this->pengirimanEntity->isPending());

        $this->pengirimanEntity->status = 3;
        $this->assertFalse($this->pengirimanEntity->isPending());
    }

    public function testIsInTransitMethod(): void
    {
        $this->pengirimanEntity->status = 1;
        $this->assertFalse($this->pengirimanEntity->isInTransit());

        $this->pengirimanEntity->status = 2;
        $this->assertTrue($this->pengirimanEntity->isInTransit());

        $this->pengirimanEntity->status = 3;
        $this->assertFalse($this->pengirimanEntity->isInTransit());
    }

    public function testDateTimeCasting(): void
    {
        $date = '2024-01-15 10:30:00';
        $this->pengirimanEntity->tanggal = $date;
        $this->pengirimanEntity->created_at = $date;
        $this->pengirimanEntity->updated_at = $date;

        $this->assertInstanceOf(\CodeIgniter\I18n\Time::class, $this->pengirimanEntity->tanggal);
        $this->assertInstanceOf(\CodeIgniter\I18n\Time::class, $this->pengirimanEntity->created_at);
        $this->assertInstanceOf(\CodeIgniter\I18n\Time::class, $this->pengirimanEntity->updated_at);
    }

    public function testGetFormattedDateMethod(): void
    {
        $this->pengirimanEntity->tanggal = '2024-01-15 10:30:00';
        $formatted = $this->pengirimanEntity->getFormattedDate();
        
        $this->assertIsString($formatted);
        $this->assertStringContainsString('2024', $formatted);
    }

    public function testGetQRDataMethod(): void
    {
        $this->pengirimanEntity->fill([
            'id_pengiriman' => 'PGR001',
            'tanggal' => '2024-01-15',
            'id_pelanggan' => 'PLG001',
            'penerima' => 'Test Receiver',
        ]);

        $qrData = $this->pengirimanEntity->getQRData();
        
        $this->assertIsString($qrData);
        $this->assertStringContainsString('PGR001', $qrData);
        $this->assertStringContainsString('PLG001', $qrData);
    }
}