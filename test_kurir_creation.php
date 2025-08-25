<?php

// Simple test script to debug kurir creation
require_once 'vendor/autoload.php';

use App\Services\KurirService;

// Initialize CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Test data
$testData = [
    'id_kurir' => 'KRR99',
    'nama' => 'Test Kurir',
    'jenis_kelamin' => 'Laki-Laki',
    'telepon' => '081234567890',
    'alamat' => 'Test Address',
    'username' => 'testkurir',
    'password' => 'password123'
];

echo "Testing kurir creation...\n";
echo "Data: " . json_encode($testData) . "\n\n";

try {
    $kurirService = new KurirService();
    $result = $kurirService->createCourier($testData);
    
    echo "Result: " . json_encode($result) . "\n";
    
    if ($result['success']) {
        echo "SUCCESS: Kurir created successfully!\n";
    } else {
        echo "ERROR: " . $result['message'] . "\n";
    }
    
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}