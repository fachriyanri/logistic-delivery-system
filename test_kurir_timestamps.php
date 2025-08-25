<?php

require_once 'vendor/autoload.php';

// Load CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

use App\Models\KurirModel;

echo "Testing Kurir Timestamps...\n\n";

$kurirModel = new KurirModel();

// Test data
$testData = [
    'id_kurir' => 'KUR99',
    'nama' => 'Test Kurir Timestamp',
    'jenis_kelamin' => 'Laki-Laki',
    'telepon' => '081234567890',
    'alamat' => 'Test Address'
];

echo "1. Testing INSERT with timestamps...\n";
echo "Data to insert: " . json_encode($testData) . "\n";

// Delete if exists first
$kurirModel->delete('KUR99');

// Insert new record
$result = $kurirModel->saveKurir($testData);

if ($result) {
    echo "✅ Insert successful!\n";
    
    // Check the record
    $saved = $kurirModel->find('KUR99');
    if ($saved) {
        echo "📅 Created at: " . ($saved->created_at ?? 'NULL') . "\n";
        echo "📅 Updated at: " . ($saved->updated_at ?? 'NULL') . "\n";
        
        if ($saved->created_at && $saved->updated_at) {
            echo "✅ Timestamps are properly set!\n";
        } else {
            echo "❌ Timestamps are still NULL!\n";
        }
    }
} else {
    echo "❌ Insert failed!\n";
    echo "Errors: " . json_encode($kurirModel->errors()) . "\n";
}

echo "\n2. Testing UPDATE with timestamps...\n";

// Update the record
$updateData = [
    'nama' => 'Updated Test Kurir',
    'alamat' => 'Updated Address'
];

$updateResult = $kurirModel->saveKurir($updateData, 'KUR99');

if ($updateResult) {
    echo "✅ Update successful!\n";
    
    // Check the updated record
    $updated = $kurirModel->find('KUR99');
    if ($updated) {
        echo "📅 Created at: " . ($updated->created_at ?? 'NULL') . "\n";
        echo "📅 Updated at: " . ($updated->updated_at ?? 'NULL') . "\n";
        
        if ($updated->updated_at) {
            echo "✅ Updated timestamp is properly set!\n";
        } else {
            echo "❌ Updated timestamp is still NULL!\n";
        }
    }
} else {
    echo "❌ Update failed!\n";
    echo "Errors: " . json_encode($kurirModel->errors()) . "\n";
}

// Clean up
echo "\n3. Cleaning up test data...\n";
$kurirModel->delete('KUR99');
echo "✅ Test completed!\n";