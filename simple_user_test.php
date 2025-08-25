<?php

// Simple user table test without CI4 bootstrap
$host = 'localhost';
$dbname = 'puninar_logistic';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== SIMPLE USER TABLE TEST ===\n\n";
    
    // Check if user table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'user'");
    $tableExists = $stmt->rowCount() > 0;
    echo "1. User table exists: " . ($tableExists ? "YES" : "NO") . "\n";
    
    if (!$tableExists) {
        echo "ERROR: User table does not exist!\n";
        exit;
    }
    
    // Get table structure
    echo "\n2. User table structure:\n";
    $stmt = $pdo->query("DESCRIBE user");
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fields as $field) {
        echo "   - {$field['Field']}: {$field['Type']} {$field['Null']} {$field['Key']} {$field['Default']}\n";
    }
    
    // Check existing data
    echo "\n3. Current user table data:\n";
    $stmt = $pdo->query("SELECT * FROM user");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "   Total records: " . count($users) . "\n";
    foreach ($users as $user) {
        echo "   - ID: {$user['id_user']}, Username: {$user['username']}, Level: {$user['level']}\n";
    }
    
    // Test simple insert
    echo "\n4. Testing simple user insert:\n";
    $testId = 'TST' . time();
    $testUsername = 'testuser_' . time();
    $testPassword = password_hash('testpass', PASSWORD_ARGON2ID);
    
    $sql = "INSERT INTO user (id_user, username, password, level) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    echo "   Attempting to insert: ID=$testId, Username=$testUsername, Level=2\n";
    
    $result = $stmt->execute([$testId, $testUsername, $testPassword, 2]);
    
    if ($result) {
        echo "   Insert result: SUCCESS\n";
        
        // Clean up
        echo "\n5. Cleaning up test data...\n";
        $stmt = $pdo->prepare("DELETE FROM user WHERE id_user = ?");
        $stmt->execute([$testId]);
        echo "   Test data cleaned up.\n";
    } else {
        echo "   Insert result: FAILED\n";
        $errorInfo = $stmt->errorInfo();
        echo "   Error: " . $errorInfo[2] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "General error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";