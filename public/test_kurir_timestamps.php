<?php

// Simple test for kurir timestamps
require_once '../vendor/autoload.php';

// Load CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

use App\Models\KurirModel;

echo "<h2>Testing Kurir Timestamps</h2>";

$kurirModel = new KurirModel();

// Test data
$testData = [
    'id_kurir' => 'KUR99',
    'nama' => 'Test Kurir Timestamp',
    'jenis_kelamin' => 'Laki-Laki',
    'telepon' => '081234567890',
    'alamat' => 'Test Address'
];

echo "<h3>1. Testing INSERT with timestamps</h3>";
echo "<p>Data to insert: " . json_encode($testData) . "</p>";

// Delete if exists first
$kurirModel->delete('KUR99');

// Insert new record
$result = $kurirModel->saveKurir($testData);

if ($result) {
    echo "<p style='color: green;'>✅ Insert successful!</p>";
    
    // Check the record
    $saved = $kurirModel->find('KUR99');
    if ($saved) {
        echo "<p>📅 Created at: " . ($saved->created_at ?? 'NULL') . "</p>";
        echo "<p>📅 Updated at: " . ($saved->updated_at ?? 'NULL') . "</p>";
        
        if ($saved->created_at && $saved->updated_at) {
            echo "<p style='color: green;'>✅ Timestamps are properly set!</p>";
        } else {
            echo "<p style='color: red;'>❌ Timestamps are still NULL!</p>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ Insert failed!</p>";
    echo "<p>Errors: " . json_encode($kurirModel->errors()) . "</p>";
}

echo "<h3>2. Testing UPDATE with timestamps</h3>";

// Update the record
$updateData = [
    'nama' => 'Updated Test Kurir',
    'alamat' => 'Updated Address'
];

$updateResult = $kurirModel->saveKurir($updateData, 'KUR99');

if ($updateResult) {
    echo "<p style='color: green;'>✅ Update successful!</p>";
    
    // Check the updated record
    $updated = $kurirModel->find('KUR99');
    if ($updated) {
        echo "<p>📅 Created at: " . ($updated->created_at ?? 'NULL') . "</p>";
        echo "<p>📅 Updated at: " . ($updated->updated_at ?? 'NULL') . "</p>";
        
        if ($updated->updated_at) {
            echo "<p style='color: green;'>✅ Updated timestamp is properly set!</p>";
        } else {
            echo "<p style='color: red;'>❌ Updated timestamp is still NULL!</p>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ Update failed!</p>";
    echo "<p>Errors: " . json_encode($kurirModel->errors()) . "</p>";
}

// Clean up
echo "<h3>3. Cleaning up test data</h3>";
$kurirModel->delete('KUR99');
echo "<p style='color: green;'>✅ Test completed!</p>";

echo "<hr>";
echo "<p><strong>Summary:</strong> The KurirModel saveKurir method has been updated to manually set created_at and updated_at timestamps.</p>";
echo "<p><strong>Changes made:</strong></p>";
echo "<ul>";
echo "<li>✅ Added manual timestamp setting in saveKurir method</li>";
echo "<li>✅ For INSERT: Sets both created_at and updated_at</li>";
echo "<li>✅ For UPDATE: Sets only updated_at</li>";
echo "<li>✅ Added debug logging to track timestamp setting</li>";
echo "</ul>";
?>