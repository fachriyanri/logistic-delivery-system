<?php

// Simple user table test that can run in browser
header('Content-Type: text/plain');

// Database configuration (adjust as needed)
$host = 'localhost';
$dbname = 'puninar_logistic';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== USER TABLE DEBUG TEST ===\n\n";
    echo "Time: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Check if user table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'user'");
    $tableExists = $stmt->rowCount() > 0;
    echo "1. User table exists: " . ($tableExists ? "YES" : "NO") . "\n";
    
    if (!$tableExists) {
        echo "ERROR: User table does not exist!\n";
        echo "Available tables:\n";
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo "   - " . $row[0] . "\n";
        }
        exit;
    }
    
    // Get table structure
    echo "\n2. User table structure:\n";
    $stmt = $pdo->query("DESCRIBE user");
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($fields as $field) {
        echo sprintf("   %-15s %-20s %-8s %-8s %s\n", 
            $field['Field'], 
            $field['Type'], 
            $field['Null'], 
            $field['Key'], 
            $field['Default']
        );
    }
    
    // Check existing data
    echo "\n3. Current user table data:\n";
    $stmt = $pdo->query("SELECT * FROM user ORDER BY id_user");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "   Total records: " . count($users) . "\n";
    if (count($users) > 0) {
        echo "   Records:\n";
        foreach ($users as $user) {
            $userInfo = "ID: {$user['id_user']}, Username: {$user['username']}, Level: {$user['level']}";
            if (isset($user['is_active'])) {
                $userInfo .= ", Active: {$user['is_active']}";
            }
            echo "   - $userInfo\n";
        }
    }
    
    // Test simple insert
    echo "\n4. Testing simple user insert:\n";
    $testId = 'TST' . substr(time(), -3);
    $testUsername = 'testuser_' . substr(time(), -3);
    $testPassword = password_hash('testpass', PASSWORD_ARGON2ID);
    
    echo "   Test data:\n";
    echo "   - ID: $testId\n";
    echo "   - Username: $testUsername\n";
    echo "   - Password: [hashed]\n";
    echo "   - Level: 2\n";
    
    // Check if is_active field exists
    $hasIsActive = false;
    foreach ($fields as $field) {
        if ($field['Field'] === 'is_active') {
            $hasIsActive = true;
            break;
        }
    }
    
    if ($hasIsActive) {
        $sql = "INSERT INTO user (id_user, username, password, level, is_active) VALUES (?, ?, ?, ?, ?)";
        $params = [$testId, $testUsername, $testPassword, 2, 1];
        echo "   Using query with is_active field\n";
    } else {
        $sql = "INSERT INTO user (id_user, username, password, level) VALUES (?, ?, ?, ?)";
        $params = [$testId, $testUsername, $testPassword, 2];
        echo "   Using query without is_active field\n";
    }
    
    echo "   SQL: $sql\n";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    if ($result) {
        echo "   Insert result: SUCCESS\n";
        echo "   Affected rows: " . $stmt->rowCount() . "\n";
        
        // Verify the insert
        $stmt = $pdo->prepare("SELECT * FROM user WHERE id_user = ?");
        $stmt->execute([$testId]);
        $insertedUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($insertedUser) {
            echo "   Verification: Record found\n";
            echo "   Inserted data: " . json_encode($insertedUser, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "   Verification: Record NOT found (this is strange)\n";
        }
        
        // Clean up
        echo "\n5. Cleaning up test data...\n";
        $stmt = $pdo->prepare("DELETE FROM user WHERE id_user = ?");
        $deleteResult = $stmt->execute([$testId]);
        echo "   Cleanup result: " . ($deleteResult ? "SUCCESS" : "FAILED") . "\n";
        echo "   Deleted rows: " . $stmt->rowCount() . "\n";
        
    } else {
        echo "   Insert result: FAILED\n";
        $errorInfo = $stmt->errorInfo();
        echo "   SQLSTATE: " . $errorInfo[0] . "\n";
        echo "   Error code: " . $errorInfo[1] . "\n";
        echo "   Error message: " . $errorInfo[2] . "\n";
    }
    
    // Test with CodeIgniter-style data
    echo "\n6. Testing with CodeIgniter-style data (like KurirService uses):\n";
    $testId2 = 'USR' . str_pad(99, 2, '0', STR_PAD_LEFT);
    $testUsername2 = 'kurir_test_' . substr(time(), -3);
    $testPassword2 = password_hash('password123', PASSWORD_ARGON2ID);
    
    echo "   Test data 2:\n";
    echo "   - ID: $testId2\n";
    echo "   - Username: $testUsername2\n";
    echo "   - Level: 2 (kurir level)\n";
    
    if ($hasIsActive) {
        $sql2 = "INSERT INTO user (id_user, username, password, level, is_active) VALUES (?, ?, ?, ?, ?)";
        $params2 = [$testId2, $testUsername2, $testPassword2, 2, 1];
    } else {
        $sql2 = "INSERT INTO user (id_user, username, password, level) VALUES (?, ?, ?, ?)";
        $params2 = [$testId2, $testUsername2, $testPassword2, 2];
    }
    
    $stmt2 = $pdo->prepare($sql2);
    $result2 = $stmt2->execute($params2);
    
    if ($result2) {
        echo "   Insert result 2: SUCCESS\n";
        
        // Clean up
        $stmt = $pdo->prepare("DELETE FROM user WHERE id_user = ?");
        $stmt->execute([$testId2]);
        echo "   Cleanup 2: SUCCESS\n";
    } else {
        echo "   Insert result 2: FAILED\n";
        $errorInfo2 = $stmt2->errorInfo();
        echo "   Error 2: " . $errorInfo2[2] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "General error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "If you see this message, the script ran successfully.\n";
echo "Check the results above for any issues.\n";