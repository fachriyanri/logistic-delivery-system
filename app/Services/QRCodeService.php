<?php

namespace App\Services;

use CodeIgniter\Config\Services;
use Endroid\QrCode\ErrorCorrectionLevel;

/**
 * QR Code Service
 * Handles QR code generation and mobile scanning functionality
 */
class QRCodeService
{
    protected $config;
    protected $exportPath;

    public function __construct()
    {
        $this->config = config('App');
        $this->exportPath = WRITEPATH . 'uploads/qrcodes/';

        // Ensure export directory exists
        if (!is_dir($this->exportPath)) {
            mkdir($this->exportPath, 0755, true);
        }
    }

    /**
     * Generate QR code for shipment
     */
    public function generateShipmentQR(string $shipmentId, array $options = []): array
    {
        $defaultOptions = [
            'size' => 'M',
            'errorCorrection' => 'M',
            'format' => 'PNG',
            'includeUrl' => true,
            'mobileOptimized' => true
        ];

        $options = array_merge($defaultOptions, $options);

        // Create QR data
        $qrData = $this->createShipmentQRData($shipmentId, $options);

        // Generate QR code
        $filename = $this->generateQRCode($qrData, $shipmentId, $options);

        return [
            'success' => true,
            'filename' => $filename,
            'path' => $this->exportPath . $filename,
            'url' => base_url('writable/uploads/qrcodes/' . $filename),
            'data' => $qrData,
            'mobile_url' => $this->generateMobileQRUrl($shipmentId)
        ];
    }

    /**
     * Create QR data for shipment
     */
    protected function createShipmentQRData(string $shipmentId, array $options): string
    {
        if ($options['includeUrl']) {
            // Create mobile-friendly URL for QR scanning
            return base_url("track/{$shipmentId}");
        }

        // Simple shipment ID
        return $shipmentId;
    }

    /**
     * Generate QR code file
     */
    public function generateQRCode(string $data, string $identifier, array $options): string
    {
        $filename = "qr_{$identifier}_{" . date('YmdHis') . "}.{$options['format']}";
        $filepath = $this->exportPath . $filename;

        // Use different QR libraries based on availability
        if ($this->generateWithEndroid($data, $filepath, $options)) {
            return $filename;
        } elseif ($this->generateWithSimpleQR($data, $filepath, $options)) {
            return $filename;
        } else {
            // Fallback to online service
            return $this->generateWithOnlineService($data, $identifier, $options);
        }
    }

    /**
     * Generate QR code using Endroid QR Code library (preferred)
     */
    protected function generateWithEndroid(string $data, string $filepath, array $options): bool
    {
        try {
            // Check if Endroid QR Code is available via Composer
            if (!class_exists('Endroid\QrCode\QrCode')) {
                return false;
            }

            $qrCode = new \Endroid\QrCode\QrCode($data);
            $qrCode->setSize($this->getSizePixels($options['size']));
            $qrCode->setErrorCorrectionLevel($this->getErrorCorrectionLevel($options['errorCorrection']));

            if ($options['mobileOptimized']) {
                // Mobile optimizations
                $qrCode->setMargin(10);
                $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0]);
                $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255]);
            }

            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);

            file_put_contents($filepath, $result->getString());
            return true;
        } catch (\Exception $e) {
            log_message('error', 'QR Code generation failed with Endroid: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate QR code using simple QR library
     */
    protected function generateWithSimpleQR(string $data, string $filepath, array $options): bool
    {
        try {
            // Check if phpqrcode library is available
            $qrLibPath = APPPATH . 'ThirdParty/phpqrcode/qrlib.php';
            if (!file_exists($qrLibPath)) {
                return false;
            }

            require_once $qrLibPath;

            if (!class_exists('QRcode')) {
                return false;
            }

            $errorLevel = $this->getQRLibErrorLevel($options['errorCorrection']);
            $size = $this->getSizeForQRLib($options['size']);

            \QRcode::png($data, $filepath, $errorLevel, $size, 2);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'QR Code generation failed with QRLib: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate QR code using online service (fallback)
     */
    protected function generateWithOnlineService(string $data, string $identifier, array $options): string
    {
        $size = $this->getSizePixels($options['size']);
        $url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($data);

        $filename = "qr_{$identifier}_online.png";
        $filepath = $this->exportPath . $filename;

        try {
            $qrImage = file_get_contents($url);
            if ($qrImage !== false) {
                file_put_contents($filepath, $qrImage);
                return $filename;
            }
        } catch (\Exception $e) {
            log_message('error', 'Online QR Code generation failed: ' . $e->getMessage());
        }

        // Ultimate fallback - create a simple text file
        $filename = "qr_{$identifier}_fallback.txt";
        file_put_contents($this->exportPath . $filename, $data);
        return $filename;
    }

    /**
     * Generate mobile-optimized QR URL
     */
    protected function generateMobileQRUrl(string $shipmentId): string
    {
        return base_url("mobile/track/{$shipmentId}");
    }

    /**
     * Validate QR code data
     */
    public function validateQRData(string $qrData): array
    {
        // Check if it's a shipment tracking URL
        if (preg_match('/\/track\/([A-Za-z0-9]+)$/', $qrData, $matches)) {
            return [
                'valid' => true,
                'type' => 'shipment_tracking',
                'shipment_id' => $matches[1]
            ];
        }

        // Check if it's a direct shipment ID
        if (preg_match('/^[A-Za-z0-9]+$/', $qrData)) {
            return [
                'valid' => true,
                'type' => 'shipment_id',
                'shipment_id' => $qrData
            ];
        }

        return [
            'valid' => false,
            'type' => 'unknown',
            'error' => 'Invalid QR code format'
        ];
    }

    /**
     * Create mobile QR scanner interface data
     */
    public function getMobileScannerConfig(): array
    {
        return [
            'camera_constraints' => [
                'video' => [
                    'facingMode' => 'environment', // Back camera
                    'width' => ['ideal' => 1280],
                    'height' => ['ideal' => 720]
                ]
            ],
            'scan_region' => [
                'width' => 250,
                'height' => 250
            ],
            'scan_frequency' => 10, // Scans per second
            'success_vibration' => [200], // Vibration pattern
            'error_vibration' => [100, 100, 100],
            'auto_stop' => true,
            'torch_support' => true
        ];
    }

    /**
     * Generate QR code for delivery note
     */
    public function generateDeliveryNoteQR(array $deliveryData): array
    {
        $qrData = json_encode([
            'type' => 'delivery_note',
            'shipment_id' => $deliveryData['shipment_id'],
            'delivery_date' => $deliveryData['delivery_date'] ?? date('Y-m-d'),
            'recipient' => $deliveryData['recipient'] ?? '',
            'verification_code' => $this->generateVerificationCode()
        ]);

        $filename = $this->generateQRCode(
            $qrData,
            'delivery_' . $deliveryData['shipment_id'],
            ['mobileOptimized' => true, 'size' => 'L']
        );

        return [
            'success' => true,
            'filename' => $filename,
            'path' => $this->exportPath . $filename,
            'url' => base_url('writable/uploads/qrcodes/' . $filename),
            'verification_code' => $this->getVerificationCodeFromData($qrData)
        ];
    }

    /**
     * Generate verification code for delivery
     */
    protected function generateVerificationCode(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 6));
    }

    /**
     * Extract verification code from QR data
     */
    protected function getVerificationCodeFromData(string $qrData): string
    {
        $data = json_decode($qrData, true);
        return $data['verification_code'] ?? '';
    }

    /**
     * Helper methods for size and error correction
     */
    protected function getSizePixels(string $size): int
    {
        $sizes = [
            'S' => 150,
            'M' => 250,
            'L' => 350,
            'XL' => 500
        ];

        return $sizes[$size] ?? $sizes['M'];
    }

    protected function getSizeForQRLib(string $size): int
    {
        $sizes = [
            'S' => 3,
            'M' => 5,
            'L' => 7,
            'XL' => 10
        ];

        return $sizes[$size] ?? $sizes['M'];
    }

    protected function getErrorCorrectionLevel(string $level): ErrorCorrectionLevel
    {
        return match (strtoupper($level)) {
            'L' => ErrorCorrectionLevel::Low,
            'Q' => ErrorCorrectionLevel::Quartile,
            'H' => ErrorCorrectionLevel::High,
            default => ErrorCorrectionLevel::Medium, // Catches 'M' and is a safe default
        };
    }

    protected function getQRLibErrorLevel(string $level): string
    {
        $levels = [
            'L' => QR_ECLEVEL_L,
            'M' => QR_ECLEVEL_M,
            'Q' => QR_ECLEVEL_Q,
            'H' => QR_ECLEVEL_H
        ];

        return $levels[$level] ?? QR_ECLEVEL_M;
    }

    /**
     * Clean up old QR code files
     */
    public function cleanupOldQRCodes(int $daysOld = 30): int
    {
        $deleted = 0;
        $cutoffTime = time() - ($daysOld * 24 * 60 * 60);

        $files = glob($this->exportPath . 'qr_*');
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                if (unlink($file)) {
                    $deleted++;
                }
            }
        }

        return $deleted;
    }
}
