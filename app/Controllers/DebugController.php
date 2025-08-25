<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class DebugController extends Controller
{
    public function userTable()
    {
        // Set content type to plain text for better readability
        $this->response->setContentType('text/plain');
        
        $output = "=== USER TABLE DEBUG ===\n\n";
        
        try {
            $db = \Config\Database::connect();
            
            // Check if user table exists
            $tableExists = $db->tableExists('user');
            $output .= "1. User table exists: " . ($tableExists ? "YES" : "NO") . "\n";
            
            if (!$tableExists) {
                $output .= "ERROR: User table does not exist!\n";
                return $this->response->setBody($output);
            }
            
            // Get table structure
            $output .= "\n2. User table structure:\n";
            $fields = $db->getFieldData('user');
            foreach ($fields as $field) {
                $output .= "   - {$field->name}: {$field->type}";
                if ($field->max_length) $output .= "({$field->max_length})";
                if ($field->nullable) $output .= " NULL"; else $output .= " NOT NULL";
                if ($field->default !== null) $output .= " DEFAULT '{$field->default}'";
                $output .= "\n";
            }
            
            // Clean up any leftover test data first
            $db->table('user')->where('id_user LIKE', 'TS%')->delete();
            
            // Test ID generation
            $output .= "\n2.5. Testing ID generation:\n";
            $userModel = new UserModel();
            $generatedId = $userModel->generateNextId();
            $output .= "   Generated ID: $generatedId\n";
            
            // Check if this ID exists
            $existsCheck = $userModel->find($generatedId);
            $output .= "   ID exists in database: " . ($existsCheck ? "YES" : "NO") . "\n";
            
            // Direct database check
            $directCheck = $db->query("SELECT COUNT(*) as count FROM user WHERE id_user = ?", [$generatedId]);
            $directExists = $directCheck->getRow()->count > 0;
            $output .= "   Direct DB check exists: " . ($directExists ? "YES" : "NO") . "\n";
            
            // Check existing data
            $output .= "\n3. Current user table data:\n";
            $users = $db->table('user')->get()->getResultArray();
            $output .= "   Total records: " . count($users) . "\n";
            foreach ($users as $user) {
                $output .= "   - ID: {$user['id_user']}, Username: {$user['username']}, Level: {$user['level']}\n";
            }
            
            // Test simple insert
            $output .= "\n4. Testing simple user insert:\n";
            $testData = [
                'id_user' => 'TST' . substr(time(), -2), // Keep it to 5 chars max
                'username' => 'testuser_' . time(),
                'password' => password_hash('testpass', PASSWORD_ARGON2ID),
                'level' => 2,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $output .= "   Data to insert: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n";
            
            $builder = $db->table('user');
            $result = $builder->insert($testData);
            
            $error = $db->error();
            $lastQuery = $db->getLastQuery();
            
            $output .= "   Insert result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
            $output .= "   Error code: " . $error['code'] . "\n";
            $output .= "   Error message: " . $error['message'] . "\n";
            $output .= "   Last query: " . $lastQuery . "\n";
            
            if ($result) {
                $output .= "\n5. Cleaning up test data...\n";
                $db->table('user')->where('id_user', $testData['id_user'])->delete();
                $output .= "   Test data cleaned up.\n";
            }
            
            // Test UserModel
            $output .= "\n6. Testing UserModel:\n";
            $userModel = new UserModel();
            $testData2 = [
                'id_user' => 'TS2' . substr(time(), -2), // Keep it to 5 chars max
                'username' => 'testuser2_' . time(),
                'password' => password_hash('testpass', PASSWORD_ARGON2ID), // Pre-hash the password
                'level' => 2,
                'is_active' => 1
            ];
            
            $output .= "   Testing UserModel insert...\n";
            $output .= "   UserModel data: " . json_encode($testData2, JSON_PRETTY_PRINT) . "\n";
            
            // Try with validation disabled first
            $userModel->skipValidation(true);
            $modelResult = $userModel->insert($testData2);
            $userModel->skipValidation(false);
            
            // If model insert succeeded, check the timestamps
            if ($modelResult) {
                $insertedUser = $userModel->find($testData2['id_user']);
                if ($insertedUser) {
                    $output .= "   Inserted user timestamps:\n";
                    $output .= "   - created_at: " . ($insertedUser->created_at ?? 'NULL') . "\n";
                    $output .= "   - updated_at: " . ($insertedUser->updated_at ?? 'NULL') . "\n";
                }
            }
            
            $modelErrors = $userModel->errors();
            
            $output .= "   Model insert result: " . ($modelResult ? "SUCCESS" : "FAILED") . "\n";
            if ($modelErrors) {
                $output .= "   Model errors: " . json_encode($modelErrors, JSON_PRETTY_PRINT) . "\n";
            }
            
            // Get database error from model
            $modelDbError = $userModel->db->error();
            if (!empty($modelDbError['message'])) {
                $output .= "   Model DB error: " . $modelDbError['message'] . "\n";
            }
            
            // Get last query from model
            $lastModelQuery = $userModel->db->getLastQuery();
            $output .= "   Model last query: " . $lastModelQuery . "\n";
            
            if ($modelResult) {
                $output .= "   Cleaning up model test data...\n";
                $userModel->delete($modelResult);
                $output .= "   Model test data cleaned up.\n";
            }
            
        } catch (\Exception $e) {
            $output .= "EXCEPTION: " . $e->getMessage() . "\n";
            $output .= "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
            $output .= "Trace: " . $e->getTraceAsString() . "\n";
        }
        
        $output .= "\n=== DEBUG COMPLETE ===\n";
        
        return $this->response->setBody($output);
    }
}