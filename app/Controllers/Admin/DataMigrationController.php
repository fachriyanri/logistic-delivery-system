<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\DataMigrationService;
use App\Services\DataValidationService;
use App\Services\UserCredentialService;
use CodeIgniter\HTTP\ResponseInterface;

class DataMigrationController extends BaseController
{
    protected DataMigrationService $migrationService;
    protected DataValidationService $validationService;
    protected UserCredentialService $credentialService;

    public function __construct()
    {
        $this->migrationService = new DataMigrationService();
        $this->validationService = new DataValidationService();
        $this->credentialService = new UserCredentialService();
    }

    /**
     * Display migration dashboard
     */
    public function index(): string
    {
        // Check current data status
        $db = \Config\Database::connect();
        
        $dataStatus = [
            'kategori' => $db->table('kategori')->countAllResults(),
            'barang' => $db->table('barang')->countAllResults(),
            'pelanggan' => $db->table('pelanggan')->countAllResults(),
            'kurir' => $db->table('kurir')->countAllResults(),
            'pengiriman' => $db->table('pengiriman')->countAllResults(),
            'detail_pengiriman' => $db->table('detail_pengiriman')->countAllResults(),
        ];

        // Check for backup tables
        $backupTables = [];
        $tables = ['kategori', 'barang', 'pelanggan', 'kurir', 'pengiriman', 'detail_pengiriman'];
        
        foreach ($tables as $table) {
            if ($db->tableExists($table . '_backup')) {
                $count = $db->table($table . '_backup')->countAllResults();
                $backupTables[$table] = $count;
            }
        }

        $data = [
            'title' => 'Data Migration',
            'dataStatus' => $dataStatus,
            'backupTables' => $backupTables,
        ];

        return view('admin/data_migration/index', $data);
    }

    /**
     * Migrate from backup tables
     */
    public function migrateFromBackup(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        try {
            $log = $this->migrationService->migrateFromBackupTables();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Migration completed successfully',
                'log' => $log
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Migration failed: ' . $e->getMessage(),
                'log' => $this->migrationService->getMigrationLog()
            ]);
        }
    }

    /**
     * Migrate from old database
     */
    public function migrateFromOldDatabase(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        $oldDatabase = $this->request->getPost('old_database');
        
        if (empty($oldDatabase)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Old database name is required'
            ]);
        }

        try {
            $log = $this->migrationService->migrateFromOldDatabase($oldDatabase);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Migration completed successfully',
                'log' => $log
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Migration failed: ' . $e->getMessage(),
                'log' => $this->migrationService->getMigrationLog()
            ]);
        }
    }

    /**
     * Import from SQL dump
     */
    public function importSQLDump(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        $file = $this->request->getFile('sql_file');
        
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Valid SQL file is required'
            ]);
        }

        try {
            // Move uploaded file to temp location
            $tempPath = WRITEPATH . 'uploads/temp_' . time() . '.sql';
            $file->move(dirname($tempPath), basename($tempPath));
            
            $log = $this->migrationService->importFromSQLDump($tempPath);
            
            // Clean up temp file
            unlink($tempPath);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'SQL import completed successfully',
                'log' => $log
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'SQL import failed: ' . $e->getMessage(),
                'log' => $this->migrationService->getMigrationLog()
            ]);
        }
    }

    /**
     * Verify data integrity
     */
    public function verifyIntegrity(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        try {
            $integrity = $this->migrationService->verifyDataIntegrity();
            
            return $this->response->setJSON([
                'success' => true,
                'integrity' => $integrity
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Integrity check failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get current data status
     */
    public function getDataStatus(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        try {
            $db = \Config\Database::connect();
            
            $status = [
                'kategori' => $db->table('kategori')->countAllResults(),
                'barang' => $db->table('barang')->countAllResults(),
                'pelanggan' => $db->table('pelanggan')->countAllResults(),
                'kurir' => $db->table('kurir')->countAllResults(),
                'pengiriman' => $db->table('pengiriman')->countAllResults(),
                'detail_pengiriman' => $db->table('detail_pengiriman')->countAllResults(),
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'status' => $status
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to get data status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Validate migrated data
     */
    public function validateData(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        try {
            $validation = $this->validationService->validateAllData();
            
            return $this->response->setJSON([
                'success' => true,
                'validation' => $validation
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data validation failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Clean up invalid data
     */
    public function cleanupData(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        try {
            $cleanup = $this->validationService->cleanupInvalidData();
            
            return $this->response->setJSON([
                'success' => true,
                'cleanup' => $cleanup
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data cleanup failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate data quality report
     */
    public function generateQualityReport(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        try {
            $report = $this->validationService->generateDataQualityReport();
            
            // Save report to file
            $filename = 'data_quality_report_' . date('Y-m-d_H-i-s') . '.json';
            $filepath = WRITEPATH . 'logs/' . $filename;
            
            if (!is_dir(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }
            
            file_put_contents($filepath, json_encode($report, JSON_PRETTY_PRINT));
            
            return $this->response->setJSON([
                'success' => true,
                'report' => $report,
                'file' => $filename
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Report generation failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update user credentials
     */
    public function updateUserCredentials(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        try {
            $result = $this->credentialService->completeCredentialUpdate();
            
            return $this->response->setJSON([
                'success' => $result['success'],
                'result' => $result
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User credential update failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create default users
     */
    public function createDefaultUsers(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        try {
            $result = $this->credentialService->createDefaultUsers();
            
            return $this->response->setJSON([
                'success' => $result['success'],
                'result' => $result
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Default user creation failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Validate user credentials
     */
    public function validateUserCredentials(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        try {
            $validation = $this->credentialService->validateUserCredentials();
            
            return $this->response->setJSON([
                'success' => true,
                'validation' => $validation
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User credential validation failed: ' . $e->getMessage()
            ]);
        }
    }
}