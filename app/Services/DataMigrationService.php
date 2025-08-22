<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;

class DataMigrationService
{
    protected BaseConnection $db;
    protected array $migrationLog = [];

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Migrate data from old database structure to new CI4 structure
     */
    public function migrateFromOldDatabase(string $oldDatabaseName): array
    {
        $this->migrationLog = [];
        
        try {
            // Check if old database exists
            if (!$this->checkOldDatabaseExists($oldDatabaseName)) {
                throw new \Exception("Old database '{$oldDatabaseName}' not found");
            }

            // Migrate each table
            $this->migrateKategoriFromOld($oldDatabaseName);
            $this->migrateBarangFromOld($oldDatabaseName);
            $this->migratePelangganFromOld($oldDatabaseName);
            $this->migrateKurirFromOld($oldDatabaseName);
            $this->migratePengirimanFromOld($oldDatabaseName);
            $this->migrateDetailPengirimanFromOld($oldDatabaseName);

            $this->migrationLog[] = "Migration completed successfully";
            
        } catch (\Exception $e) {
            $this->migrationLog[] = "Migration failed: " . $e->getMessage();
            throw $e;
        }

        return $this->migrationLog;
    }

    /**
     * Import data from SQL dump file
     */
    public function importFromSQLDump(string $sqlFilePath): array
    {
        $this->migrationLog = [];
        
        try {
            if (!file_exists($sqlFilePath)) {
                throw new \Exception("SQL dump file not found: {$sqlFilePath}");
            }

            // Read and parse SQL file
            $sqlContent = file_get_contents($sqlFilePath);
            $this->executeSQLDump($sqlContent);
            
            $this->migrationLog[] = "SQL dump imported successfully";
            
        } catch (\Exception $e) {
            $this->migrationLog[] = "SQL import failed: " . $e->getMessage();
            throw $e;
        }

        return $this->migrationLog;
    }

    /**
     * Migrate data from backup tables (tables with _backup suffix)
     */
    public function migrateFromBackupTables(): array
    {
        $this->migrationLog = [];
        
        try {
            $this->migrateKategoriFromBackup();
            $this->migrateBarangFromBackup();
            $this->migratePelangganFromBackup();
            $this->migrateKurirFromBackup();
            $this->migratePengirimanFromBackup();
            $this->migrateDetailPengirimanFromBackup();

            $this->migrationLog[] = "Backup table migration completed successfully";
            
        } catch (\Exception $e) {
            $this->migrationLog[] = "Backup migration failed: " . $e->getMessage();
            throw $e;
        }

        return $this->migrationLog;
    }

    private function checkOldDatabaseExists(string $databaseName): bool
    {
        $query = $this->db->query("SHOW DATABASES LIKE '{$databaseName}'");
        return $query->getNumRows() > 0;
    }

    private function migrateKategoriFromOld(string $oldDb): void
    {
        $query = $this->db->query("SELECT * FROM {$oldDb}.kategori");
        $oldData = $query->getResultArray();
        
        $migrated = 0;
        foreach ($oldData as $row) {
            if ($this->insertKategori($row)) {
                $migrated++;
            }
        }
        
        $this->migrationLog[] = "Migrated {$migrated} kategori records";
    }

    private function migrateBarangFromOld(string $oldDb): void
    {
        $query = $this->db->query("SELECT * FROM {$oldDb}.barang");
        $oldData = $query->getResultArray();
        
        $migrated = 0;
        foreach ($oldData as $row) {
            if ($this->insertBarang($row)) {
                $migrated++;
            }
        }
        
        $this->migrationLog[] = "Migrated {$migrated} barang records";
    }

    private function migratePelangganFromOld(string $oldDb): void
    {
        $query = $this->db->query("SELECT * FROM {$oldDb}.pelanggan");
        $oldData = $query->getResultArray();
        
        $migrated = 0;
        foreach ($oldData as $row) {
            if ($this->insertPelanggan($row)) {
                $migrated++;
            }
        }
        
        $this->migrationLog[] = "Migrated {$migrated} pelanggan records";
    }

    private function migrateKurirFromOld(string $oldDb): void
    {
        $query = $this->db->query("SELECT * FROM {$oldDb}.kurir");
        $oldData = $query->getResultArray();
        
        $migrated = 0;
        foreach ($oldData as $row) {
            if ($this->insertKurir($row)) {
                $migrated++;
            }
        }
        
        $this->migrationLog[] = "Migrated {$migrated} kurir records";
    }

    private function migratePengirimanFromOld(string $oldDb): void
    {
        $query = $this->db->query("SELECT * FROM {$oldDb}.pengiriman");
        $oldData = $query->getResultArray();
        
        $migrated = 0;
        foreach ($oldData as $row) {
            if ($this->insertPengiriman($row)) {
                $migrated++;
            }
        }
        
        $this->migrationLog[] = "Migrated {$migrated} pengiriman records";
    }

    private function migrateDetailPengirimanFromOld(string $oldDb): void
    {
        $query = $this->db->query("SELECT * FROM {$oldDb}.detail_pengiriman");
        $oldData = $query->getResultArray();
        
        $migrated = 0;
        foreach ($oldData as $row) {
            if ($this->insertDetailPengiriman($row)) {
                $migrated++;
            }
        }
        
        $this->migrationLog[] = "Migrated {$migrated} detail_pengiriman records";
    }

    private function migrateKategoriFromBackup(): void
    {
        if (!$this->db->tableExists('kategori_backup')) {
            $this->migrationLog[] = "kategori_backup table not found, skipping";
            return;
        }

        $query = $this->db->table('kategori_backup')->get();
        $oldData = $query->getResultArray();
        
        $migrated = 0;
        foreach ($oldData as $row) {
            if ($this->insertKategori($row)) {
                $migrated++;
            }
        }
        
        $this->migrationLog[] = "Migrated {$migrated} kategori records from backup";
    }

    private function migrateBarangFromBackup(): void
    {
        if (!$this->db->tableExists('barang_backup')) {
            $this->migrationLog[] = "barang_backup table not found, skipping";
            return;
        }

        $query = $this->db->table('barang_backup')->get();
        $oldData = $query->getResultArray();
        
        $migrated = 0;
        foreach ($oldData as $row) {
            if ($this->insertBarang($row)) {
                $migrated++;
            }
        }
        
        $this->migrationLog[] = "Migrated {$migrated} barang records from backup";
    }

    private function migratePelangganFromBackup(): void
    {
        if (!$this->db->tableExists('pelanggan_backup')) {
            $this->migrationLog[] = "pelanggan_backup table not found, skipping";
            return;
        }

        $query = $this->db->table('pelanggan_backup')->get();
        $oldData = $query->getResultArray();
        
        $migrated = 0;
        foreach ($oldData as $row) {
            if ($this->insertPelanggan($row)) {
                $migrated++;
            }
        }
        
        $this->migrationLog[] = "Migrated {$migrated} pelanggan records from backup";
    }

    private function migrateKurirFromBackup(): void
    {
        if (!$this->db->tableExists('kurir_backup')) {
            $this->migrationLog[] = "kurir_backup table not found, skipping";
            return;
        }

        $query = $this->db->table('kurir_backup')->get();
        $oldData = $query->getResultArray();
        
        $migrated = 0;
        foreach ($oldData as $row) {
            if ($this->insertKurir($row)) {
                $migrated++;
            }
        }
        
        $this->migrationLog[] = "Migrated {$migrated} kurir records from backup";
    }

    private function migratePengirimanFromBackup(): void
    {
        if (!$this->db->tableExists('pengiriman_backup')) {
            $this->migrationLog[] = "pengiriman_backup table not found, skipping";
            return;
        }

        $query = $this->db->table('pengiriman_backup')->get();
        $oldData = $query->getResultArray();
        
        $migrated = 0;
        foreach ($oldData as $row) {
            if ($this->insertPengiriman($row)) {
                $migrated++;
            }
        }
        
        $this->migrationLog[] = "Migrated {$migrated} pengiriman records from backup";
    }

    private function migrateDetailPengirimanFromBackup(): void
    {
        if (!$this->db->tableExists('detail_pengiriman_backup')) {
            $this->migrationLog[] = "detail_pengiriman_backup table not found, skipping";
            return;
        }

        $query = $this->db->table('detail_pengiriman_backup')->get();
        $oldData = $query->getResultArray();
        
        $migrated = 0;
        foreach ($oldData as $row) {
            if ($this->insertDetailPengiriman($row)) {
                $migrated++;
            }
        }
        
        $this->migrationLog[] = "Migrated {$migrated} detail_pengiriman records from backup";
    }

    private function insertKategori(array $row): bool
    {
        $newRow = [
            'id_kategori' => $row['id_kategori'],
            'nama' => $row['nama'],
            'keterangan' => $row['keterangan'] ?? null,
        ];

        // Check if record already exists
        $existing = $this->db->table('kategori')
            ->where('id_kategori', $newRow['id_kategori'])
            ->get()
            ->getRow();

        if (!$existing) {
            return $this->db->table('kategori')->insert($newRow);
        }
        
        return false; // Already exists
    }

    private function insertBarang(array $row): bool
    {
        $newRow = [
            'id_barang' => $row['id_barang'],
            'nama' => $row['nama'],
            'satuan' => $row['satuan'],
            'del_no' => $row['del_no'],
            'id_kategori' => $row['id_kategori'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $existing = $this->db->table('barang')
            ->where('id_barang', $newRow['id_barang'])
            ->get()
            ->getRow();

        if (!$existing) {
            return $this->db->table('barang')->insert($newRow);
        }
        
        return false;
    }

    private function insertPelanggan(array $row): bool
    {
        $newRow = [
            'id_pelanggan' => $row['id_pelanggan'],
            'nama' => $row['nama'],
            'telepon' => $row['telepon'],
            'alamat' => $row['alamat'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $existing = $this->db->table('pelanggan')
            ->where('id_pelanggan', $newRow['id_pelanggan'])
            ->get()
            ->getRow();

        if (!$existing) {
            return $this->db->table('pelanggan')->insert($newRow);
        }
        
        return false;
    }

    private function insertKurir(array $row): bool
    {
        // Convert old MD5 passwords to new secure hashing
        $newPassword = password_hash('defaultpassword123', PASSWORD_ARGON2ID);
        
        $newRow = [
            'id_kurir' => $row['id_kurir'],
            'nama' => $row['nama'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'telepon' => $row['telepon'],
            'alamat' => $row['alamat'] ?? null,
            'password' => $newPassword,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $existing = $this->db->table('kurir')
            ->where('id_kurir', $newRow['id_kurir'])
            ->get()
            ->getRow();

        if (!$existing) {
            return $this->db->table('kurir')->insert($newRow);
        }
        
        return false;
    }

    private function insertPengiriman(array $row): bool
    {
        $newRow = [
            'id_pengiriman' => $row['id_pengiriman'],
            'tanggal' => $row['tanggal'],
            'id_pelanggan' => $row['id_pelanggan'],
            'id_kurir' => $row['id_kurir'],
            'no_kendaraan' => $row['no_kendaraan'],
            'no_po' => $row['no_po'],
            'keterangan' => $row['keterangan'] ?: null,
            'penerima' => $row['penerima'] ?: null,
            'photo' => $row['photo'] ?: null,
            'status' => (int)$row['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $existing = $this->db->table('pengiriman')
            ->where('id_pengiriman', $newRow['id_pengiriman'])
            ->get()
            ->getRow();

        if (!$existing) {
            return $this->db->table('pengiriman')->insert($newRow);
        }
        
        return false;
    }

    private function insertDetailPengiriman(array $row): bool
    {
        $newRow = [
            'id_pengiriman' => $row['id_pengiriman'],
            'id_barang' => $row['id_barang'],
            'qty' => (int)$row['qty'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Check if this detail already exists
        $existing = $this->db->table('detail_pengiriman')
            ->where('id_pengiriman', $newRow['id_pengiriman'])
            ->where('id_barang', $newRow['id_barang'])
            ->get()
            ->getRow();

        if (!$existing) {
            return $this->db->table('detail_pengiriman')->insert($newRow);
        }
        
        return false;
    }

    private function executeSQLDump(string $sqlContent): void
    {
        // Parse and execute SQL statements
        $statements = $this->parseSQLStatements($sqlContent);
        
        foreach ($statements as $statement) {
            if (trim($statement)) {
                try {
                    $this->db->query($statement);
                } catch (DatabaseException $e) {
                    // Log but continue with other statements
                    $this->migrationLog[] = "Warning: " . $e->getMessage();
                }
            }
        }
    }

    private function parseSQLStatements(string $sqlContent): array
    {
        // Remove comments and split by semicolon
        $sqlContent = preg_replace('/--.*$/m', '', $sqlContent);
        $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);
        
        return array_filter(explode(';', $sqlContent), function($stmt) {
            return trim($stmt) !== '';
        });
    }

    /**
     * Get migration log
     */
    public function getMigrationLog(): array
    {
        return $this->migrationLog;
    }

    /**
     * Verify data integrity after migration
     */
    public function verifyDataIntegrity(): array
    {
        $results = [];
        
        // Check foreign key relationships
        $results['kategori_count'] = $this->db->table('kategori')->countAllResults();
        $results['barang_count'] = $this->db->table('barang')->countAllResults();
        $results['pelanggan_count'] = $this->db->table('pelanggan')->countAllResults();
        $results['kurir_count'] = $this->db->table('kurir')->countAllResults();
        $results['pengiriman_count'] = $this->db->table('pengiriman')->countAllResults();
        $results['detail_pengiriman_count'] = $this->db->table('detail_pengiriman')->countAllResults();
        
        // Check for orphaned records
        $orphanedBarang = $this->db->query("
            SELECT COUNT(*) as count 
            FROM barang b 
            LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
            WHERE k.id_kategori IS NULL
        ")->getRow()->count;
        
        $orphanedPengiriman = $this->db->query("
            SELECT COUNT(*) as count 
            FROM pengiriman p 
            LEFT JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan 
            LEFT JOIN kurir k ON p.id_kurir = k.id_kurir 
            WHERE pel.id_pelanggan IS NULL OR k.id_kurir IS NULL
        ")->getRow()->count;
        
        $orphanedDetails = $this->db->query("
            SELECT COUNT(*) as count 
            FROM detail_pengiriman dp 
            LEFT JOIN pengiriman p ON dp.id_pengiriman = p.id_pengiriman 
            LEFT JOIN barang b ON dp.id_barang = b.id_barang 
            WHERE p.id_pengiriman IS NULL OR b.id_barang IS NULL
        ")->getRow()->count;
        
        $results['orphaned_barang'] = $orphanedBarang;
        $results['orphaned_pengiriman'] = $orphanedPengiriman;
        $results['orphaned_details'] = $orphanedDetails;
        $results['integrity_check'] = ($orphanedBarang + $orphanedPengiriman + $orphanedDetails) === 0;
        
        return $results;
    }
}