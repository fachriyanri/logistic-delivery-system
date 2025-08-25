<?php

// Debug script to test user table structure and insertion
require_once 'vendor/autoload.php';

use Config\Database;

// Initialize CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

echo "=== USER TABLE DEBUG ===\n\n";

try {
    $db = Database::connect();
    
    // Check if user table exists
    $tableExists = $db->tableExists('user');
    echo "1. User table exists: " . ($tableExists ? "YES" : "NO") . "\n";
    
    if (!$tableExists) {
        echo "ERROR: User table does not exist!\n";
        exit;
    }
    
    // Get table structure
    echo "\n2. User table structure:\n";
    $fields = $db->getFieldData('user');
    foreach ($fields as $field) {
        echo "   - {$field->name}: {$field->type}";
        if ($field->max_length) echo "({$field->max_length})";
        if ($field->nullable) echo " NULL"; else echo " NOT NULL";
        if ($field->default !== null) echo " DEFAULT '{$field->default}'";
        echo "\n";
    }
    
    // Test simple insert
    echo "\n3. Testing simple user insert:\n";
    $testData = [
        'id_user' => 'TST01',
        'username' => 'testuser_' . time(),
        'password' => password_hash('testpass', PASSWORD_ARGON2ID),
        'level' => 2
    ];
    
    echo "   Data to insert: " . json_encode($testData) . "\n";
    
    $builder = $db->table('user');
    $result = $builder->insert($testData);
    
    $error = $db->error();
    $lastQuery = $db->getLastQuery();
    
    echo "   Insert result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    echo "   Error code: " . $error['code'] . "\n";
    echo "   Error message: " . $error['message'] . "\n";
    echo "   Last query: " . $lastQuery . "\n";
    
    if ($result) {
        echo "\n4. Cleaning up test data...\n";
        $db->table('user')->where('id_user', 'TST01')->delete();
        echo "   Test data cleaned up.\n";
    }
    
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";