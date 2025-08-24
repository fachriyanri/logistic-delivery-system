<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\KategoriService;

class DebugController extends BaseController
{
    public function testKategori()
    {
        $kategoriService = new KategoriService();
        
        echo "<h1>Category System Debug</h1>";
        
        // Cleanup any previous test records
        echo "<h2>Cleanup Previous Test Records</h2>";
        $db = \Config\Database::connect();
        $cleanupResult = $db->query("DELETE FROM kategori WHERE nama LIKE 'Test%'");
        echo "Cleaned up test records<br>";
        
        // Test 1: Get all categories
        echo "<h2>Test 1: Get All Categories</h2>";
        [$categories, $total] = $kategoriService->getAllCategories();
        echo "Total categories: " . $total . "<br>";
        foreach ($categories as $cat) {
            echo "- {$cat->id_kategori}: {$cat->nama}<br>";
        }
        
        // Test 2: Generate next ID
        echo "<h2>Test 2: Generate Next ID</h2>";
        $nextId = $kategoriService->generateNextId();
        echo "Next ID: " . $nextId . "<br>";
        
        // Test 3: Test validation
        echo "<h2>Test 3: Test Validation</h2>";
        $testData = [
            'id_kategori' => $nextId,
            'nama' => 'Test Category',
            'keterangan' => 'Test description'
        ];
        $errors = $kategoriService->validateCategoryData($testData);
        if (empty($errors)) {
            echo "Validation passed<br>";
        } else {
            echo "Validation errors:<br>";
            foreach ($errors as $error) {
                echo "- " . $error . "<br>";
            }
        }
        
        // Test 4: Test category creation
        echo "<h2>Test 4: Test Category Creation</h2>";
        
        // Find a unique test ID
        $kategoriModel = new \App\Models\KategoriModel();
        $testId = 'KTG99';
        $counter = 99;
        
        while ($kategoriModel->find($testId) && $counter > 90) {
            $counter--;
            $testId = 'KTG' . str_pad($counter, 2, '0', STR_PAD_LEFT);
        }
        
        $testData['id_kategori'] = $testId;
        $testData['nama'] = 'Test Category ' . $counter;
        
        echo "Using unique test data: " . json_encode($testData) . "<br>";
        
        // Test direct model insert first
        echo "<h3>4a: Direct Model Test</h3>";
        
        $insertResult = $kategoriModel->insert($testData);
        if ($insertResult) {
            echo "Direct model insert successful: " . $insertResult . "<br>";
            
            // Clean up
            $kategoriModel->delete($testData['id_kategori']);
        } else {
            echo "Direct model insert failed<br>";
            $errors = $kategoriModel->errors();
            if (!empty($errors)) {
                echo "Model errors: " . json_encode($errors) . "<br>";
            }
            
            // Get validation errors
            $validation = \Config\Services::validation();
            $validationErrors = $validation->getErrors();
            if (!empty($validationErrors)) {
                echo "Validation errors: " . json_encode($validationErrors) . "<br>";
            }
            
            // Check if validation was run
            echo "Model validation rules: " . json_encode($kategoriModel->getValidationRules()) . "<br>";
            
            // Try without validation
            echo "<h4>Testing without validation:</h4>";
            $kategoriModel->skipValidation(true);
            
            // Make sure we use a unique ID for this test too
            $testData2 = $testData;
            $testData2['id_kategori'] = 'KTG' . str_pad($counter - 1, 2, '0', STR_PAD_LEFT);
            $testData2['nama'] = 'Test No Validation ' . ($counter - 1);
            
            $insertResult2 = $kategoriModel->insert($testData2);
            if ($insertResult2) {
                echo "Insert without validation: SUCCESS<br>";
                $kategoriModel->delete($testData2['id_kategori']);
            } else {
                echo "Insert without validation: FAILED<br>";
                echo "Last query: " . $kategoriModel->db->getLastQuery() . "<br>";
            }
            
            // Check database connection
            $db = \Config\Database::connect();
            if ($db->connID) {
                echo "Database connection: OK<br>";
            } else {
                echo "Database connection: FAILED<br>";
            }
        }
        
        echo "<h3>4b: Service Layer Test</h3>";
        
        // Debug service layer checks
        echo "Checking if ID {$testData['id_kategori']} exists: ";
        $existingById = $kategoriService->getCategoryById($testData['id_kategori']);
        echo $existingById ? "YES (found)" : "NO (not found)";
        echo "<br>";
        
        echo "Checking if name '{$testData['nama']}' exists: ";
        $existingByName = $kategoriService->getCategoryByName($testData['nama']);
        echo $existingByName ? "YES (found)" : "NO (not found)";
        echo "<br>";
        
        $result = $kategoriService->createCategory($testData);
        if ($result['success']) {
            echo "Category created successfully: " . $result['message'] . "<br>";
            
            // Test 5: Test category deletion
            echo "<h2>Test 5: Test Category Deletion</h2>";
            $deleteResult = $kategoriService->deleteCategory($testData['id_kategori']);
            if ($deleteResult['success']) {
                echo "Category deleted successfully: " . $deleteResult['message'] . "<br>";
            } else {
                echo "Delete failed: " . $deleteResult['message'] . "<br>";
            }
        } else {
            echo "Creation failed: " . $result['message'] . "<br>";
        }
        
        // Test 6: Check table structure
        echo "<h2>Test 6: Database Table Structure</h2>";
        $db = \Config\Database::connect();
        
        try {
            $query = $db->query("DESCRIBE kategori");
            $fields = $query->getResultArray();
            
            echo "Table 'kategori' structure:<br>";
            foreach ($fields as $field) {
                echo "- {$field['Field']}: {$field['Type']} " . 
                     ($field['Null'] === 'YES' ? '(nullable)' : '(not null)') . 
                     ($field['Key'] === 'PRI' ? ' PRIMARY KEY' : '') . "<br>";
            }
            
            // Test insert with raw SQL
            echo "<h3>6a: Raw SQL Insert Test</h3>";
            $testId = 'KTG' . str_pad($counter - 2, 2, '0', STR_PAD_LEFT);
            
            // Delete if exists
            $db->query("DELETE FROM kategori WHERE id_kategori = ?", [$testId]);
            
            $insertSQL = "INSERT INTO kategori (id_kategori, nama, keterangan, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
            $insertResult = $db->query($insertSQL, [$testId, 'Test Raw SQL', 'Test description']);
            
            if ($insertResult) {
                echo "Raw SQL insert successful<br>";
                
                // Verify insert
                $checkQuery = $db->query("SELECT * FROM kategori WHERE id_kategori = ?", [$testId]);
                $inserted = $checkQuery->getRow();
                if ($inserted) {
                    echo "Verified: Record exists with ID {$inserted->id_kategori}<br>";
                }
                
                // Clean up
                $db->query("DELETE FROM kategori WHERE id_kategori = ?", [$testId]);
                echo "Cleanup completed<br>";
            } else {
                echo "Raw SQL insert failed<br>";
                $error = $db->error();
                echo "Database error: " . json_encode($error) . "<br>";
            }
            
        } catch (\Exception $e) {
            echo "Database error: " . $e->getMessage() . "<br>";
        }
        
        echo "<br><a href='" . base_url('kategori') . "'>Back to Categories</a>";
    }
    
    public function testDelete($id)
    {
        $kategoriService = new KategoriService();
        
        echo "<h1>Delete Test for ID: {$id}</h1>";
        
        // Test 1: Check if category exists
        echo "<h2>Test 1: Check if category exists</h2>";
        $db = \Config\Database::connect();
        $existingCount = $db->table('kategori')->where('id_kategori', $id)->countAllResults();
        echo "Database count for ID {$id}: {$existingCount}<br>";
        
        if ($existingCount > 0) {
            $category = $db->table('kategori')->where('id_kategori', $id)->get()->getRow();
            echo "Found category: " . json_encode($category) . "<br>";
        } else {
            echo "Category not found in database<br>";
        }
        
        // Test 2: Check using service
        echo "<h2>Test 2: Check using service</h2>";
        $serviceCategory = $kategoriService->getCategoryById($id);
        echo "Service result: " . ($serviceCategory ? "Found" : "Not found") . "<br>";
        
        // Test 3: Check if in use
        echo "<h2>Test 3: Check if category is in use</h2>";
        $inUseCount = $db->table('barang')->where('id_kategori', $id)->countAllResults();
        echo "Items using this category: {$inUseCount}<br>";
        
        // Test 4: Try delete
        echo "<h2>Test 4: Try delete operation</h2>";
        if ($existingCount > 0) {
            $result = $kategoriService->deleteCategory($id);
            echo "Delete result: " . json_encode($result) . "<br>";
        } else {
            echo "Skipping delete test - category doesn't exist<br>";
        }
        
        echo "<br><a href='" . base_url('kategori') . "'>Back to Categories</a>";
    }
}