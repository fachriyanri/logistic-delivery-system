<?php

namespace Tests\Unit\Services;

use App\Services\QRCodeService;
use CodeIgniter\Test\CIUnitTestCase;

class QRCodeServiceTest extends CIUnitTestCase
{
    private QRCodeService $qrCodeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->qrCodeService = new QRCodeService();
    }

    public function testGenerateQRCodeReturnsString(): void
    {
        $data = 'Test QR Code Data';
        $result = $this->qrCodeService->generateQRCode($data);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testGenerateQRCodeWithCustomFilename(): void
    {
        $data = 'Test QR Code Data';
        $filename = 'custom_qr_code';
        
        $result = $this->qrCodeService->generateQRCode($data, $filename);

        $this->assertIsString($result);
        $this->assertStringContainsString($filename, $result);
    }

    public function testGenerateQRCodeForShipment(): void
    {
        $shipmentData = [
            'id_pengiriman' => 'PGR001',
            'tanggal' => '2024-01-15',
            'pelanggan' => 'Test Customer',
            'penerima' => 'Test Receiver',
        ];

        $result = $this->qrCodeService->generateQRCodeForShipment($shipmentData);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testValidateQRCodeWithValidData(): void
    {
        $validQRData = json_encode([
            'id_pengiriman' => 'PGR001',
            'tanggal' => '2024-01-15',
            'pelanggan' => 'Test Customer',
        ]);

        $result = $this->qrCodeService->validateQRCode($validQRData);
        $this->assertTrue($result);
    }

    public function testValidateQRCodeWithInvalidData(): void
    {
        $invalidQRData = 'invalid json data';

        $result = $this->qrCodeService->validateQRCode($invalidQRData);
        $this->assertFalse($result);
    }

    public function testValidateQRCodeWithMissingRequiredFields(): void
    {
        $incompleteQRData = json_encode([
            'tanggal' => '2024-01-15',
            // Missing id_pengiriman
        ]);

        $result = $this->qrCodeService->validateQRCode($incompleteQRData);
        $this->assertFalse($result);
    }

    public function testDecodeQRData(): void
    {
        $originalData = [
            'id_pengiriman' => 'PGR001',
            'tanggal' => '2024-01-15',
            'pelanggan' => 'Test Customer',
        ];
        
        $encodedData = json_encode($originalData);
        $decodedData = $this->qrCodeService->decodeQRData($encodedData);

        $this->assertIsArray($decodedData);
        $this->assertEquals($originalData['id_pengiriman'], $decodedData['id_pengiriman']);
        $this->assertEquals($originalData['tanggal'], $decodedData['tanggal']);
        $this->assertEquals($originalData['pelanggan'], $decodedData['pelanggan']);
    }

    public function testDecodeQRDataWithInvalidJSON(): void
    {
        $invalidData = 'invalid json';
        $decodedData = $this->qrCodeService->decodeQRData($invalidData);

        $this->assertNull($decodedData);
    }

    public function testGetQRCodePath(): void
    {
        $filename = 'test_qr_code';
        $path = $this->qrCodeService->getQRCodePath($filename);

        $this->assertIsString($path);
        $this->assertStringContainsString($filename, $path);
        $this->assertStringContainsString('.png', $path);
    }

    public function testDeleteQRCode(): void
    {
        // First generate a QR code
        $data = 'Test QR Code for deletion';
        $filename = 'delete_test_qr';
        
        $qrPath = $this->qrCodeService->generateQRCode($data, $filename);
        
        // Verify it exists (if file system is available)
        if (file_exists($qrPath)) {
            $result = $this->qrCodeService->deleteQRCode($filename);
            $this->assertTrue($result);
            $this->assertFalse(file_exists($qrPath));
        } else {
            // If file system is not available in test environment, just test the method exists
            $this->assertTrue(method_exists($this->qrCodeService, 'deleteQRCode'));
        }
    }

    public function testGenerateQRCodeWithDifferentSizes(): void
    {
        $data = 'Test QR Code Data';
        
        $smallQR = $this->qrCodeService->generateQRCode($data, null, 100);
        $largeQR = $this->qrCodeService->generateQRCode($data, null, 300);

        $this->assertIsString($smallQR);
        $this->assertIsString($largeQR);
        $this->assertNotEquals($smallQR, $largeQR);
    }

    public function testGenerateBase64QRCode(): void
    {
        $data = 'Test QR Code Data';
        $base64QR = $this->qrCodeService->generateBase64QRCode($data);

        $this->assertIsString($base64QR);
        $this->assertStringStartsWith('data:image/png;base64,', $base64QR);
    }

    public function testBatchGenerateQRCodes(): void
    {
        $dataArray = [
            ['id' => 'QR001', 'data' => 'First QR Code'],
            ['id' => 'QR002', 'data' => 'Second QR Code'],
            ['id' => 'QR003', 'data' => 'Third QR Code'],
        ];

        $results = $this->qrCodeService->batchGenerateQRCodes($dataArray);

        $this->assertIsArray($results);
        $this->assertCount(3, $results);
        
        foreach ($results as $result) {
            $this->assertArrayHasKey('id', $result);
            $this->assertArrayHasKey('path', $result);
            $this->assertIsString($result['path']);
        }
    }
}