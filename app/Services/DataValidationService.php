<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class DataValidationService
{
    protected BaseConnection $db;
    protected array $validationLog = [];
    protected array $cleanupLog = [];

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Validate all migrated data
     */
    public function validateAllData(): array
    {
        $this->validationLog = [];
        
        $results = [
            'kategori' => $this->validateKategoriData(),
            'barang' => $this->validateBarangData(),
            'pelanggan' => $this->validatePelangganData(),
            'kurir' => $this->validateKurirData(),
            'pengiriman' => $this->validatePengirimanData(),
            'detail_pengiriman' => $this->validateDetailPengirimanData(),
            'relationships' => $this->validateRelationships(),
        ];

        $results['overall_valid'] = $this->isOverallValid($results);
        $results['validation_log'] = $this->validationLog;

        return $results;
    }

    /**
     * Clean up inconsistent or invalid data
     */
    public function cleanupInvalidData(): array
    {
        $this->cleanupLog = [];
        
        $results = [
            'duplicate_removal' => $this->removeDuplicateRecords(),
            'invalid_data_cleanup' => $this->cleanupInvalidRecords(),
            'orphaned_records' => $this->cleanupOrphanedRecords(),
            'data_normalization' => $this->normalizeData(),
        ];

        $results['cleanup_log'] = $this->cleanupLog;

        return $results;
    }

    /**
     * Validate kategori data
     */
    private function validateKategoriData(): array
    {
        $issues = [];
        
        // Check for empty or null names
        $emptyNames = $this->db->query("
            SELECT COUNT(*) as count 
            FROM kategori 
            WHERE nama IS NULL OR nama = '' OR TRIM(nama) = ''
        ")->getRow()->count;
        
        if ($emptyNames > 0) {
            $issues[] = "Found {$emptyNames} kategori records with empty names";
        }

        // Check for duplicate IDs
        $duplicateIds = $this->db->query("
            SELECT id_kategori, COUNT(*) as count 
            FROM kategori 
            GROUP BY id_kategori 
            HAVING COUNT(*) > 1
        ")->getResultArray();
        
        if (!empty($duplicateIds)) {
            $issues[] = "Found " . count($duplicateIds) . " duplicate kategori IDs";
        }

        // Check ID format (should be KTG followed by numbers)
        $invalidIds = $this->db->query("
            SELECT COUNT(*) as count 
            FROM kategori 
            WHERE id_kategori NOT REGEXP '^KTG[0-9]+$'
        ")->getRow()->count;
        
        if ($invalidIds > 0) {
            $issues[] = "Found {$invalidIds} kategori records with invalid ID format";
        }

        $this->validationLog[] = "Kategori validation: " . (empty($issues) ? "PASSED" : implode(", ", $issues));
        
        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'total_records' => $this->db->table('kategori')->countAllResults()
        ];
    }

    /**
     * Validate barang data
     */
    private function validateBarangData(): array
    {
        $issues = [];
        
        // Check for empty names
        $emptyNames = $this->db->query("
            SELECT COUNT(*) as count 
            FROM barang 
            WHERE nama IS NULL OR nama = '' OR TRIM(nama) = ''
        ")->getRow()->count;
        
        if ($emptyNames > 0) {
            $issues[] = "Found {$emptyNames} barang records with empty names";
        }

        // Check for invalid kategori references
        $invalidKategori = $this->db->query("
            SELECT COUNT(*) as count 
            FROM barang b 
            LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
            WHERE k.id_kategori IS NULL
        ")->getRow()->count;
        
        if ($invalidKategori > 0) {
            $issues[] = "Found {$invalidKategori} barang records with invalid kategori references";
        }

        // Check ID format (should be BRG followed by numbers)
        $invalidIds = $this->db->query("
            SELECT COUNT(*) as count 
            FROM barang 
            WHERE id_barang NOT REGEXP '^BRG[0-9]+$'
        ")->getRow()->count;
        
        if ($invalidIds > 0) {
            $issues[] = "Found {$invalidIds} barang records with invalid ID format";
        }

        // Check for empty satuan
        $emptySatuan = $this->db->query("
            SELECT COUNT(*) as count 
            FROM barang 
            WHERE satuan IS NULL OR satuan = '' OR TRIM(satuan) = ''
        ")->getRow()->count;
        
        if ($emptySatuan > 0) {
            $issues[] = "Found {$emptySatuan} barang records with empty satuan";
        }

        $this->validationLog[] = "Barang validation: " . (empty($issues) ? "PASSED" : implode(", ", $issues));
        
        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'total_records' => $this->db->table('barang')->countAllResults()
        ];
    }

    /**
     * Validate pelanggan data
     */
    private function validatePelangganData(): array
    {
        $issues = [];
        
        // Check for empty names
        $emptyNames = $this->db->query("
            SELECT COUNT(*) as count 
            FROM pelanggan 
            WHERE nama IS NULL OR nama = '' OR TRIM(nama) = ''
        ")->getRow()->count;
        
        if ($emptyNames > 0) {
            $issues[] = "Found {$emptyNames} pelanggan records with empty names";
        }

        // Check for invalid phone numbers
        $invalidPhones = $this->db->query("
            SELECT COUNT(*) as count 
            FROM pelanggan 
            WHERE telepon IS NULL OR telepon = '' OR LENGTH(TRIM(telepon)) < 8
        ")->getRow()->count;
        
        if ($invalidPhones > 0) {
            $issues[] = "Found {$invalidPhones} pelanggan records with invalid phone numbers";
        }

        // Check ID format (should be CST followed by numbers)
        $invalidIds = $this->db->query("
            SELECT COUNT(*) as count 
            FROM pelanggan 
            WHERE id_pelanggan NOT REGEXP '^CST[0-9]+$'
        ")->getRow()->count;
        
        if ($invalidIds > 0) {
            $issues[] = "Found {$invalidIds} pelanggan records with invalid ID format";
        }

        $this->validationLog[] = "Pelanggan validation: " . (empty($issues) ? "PASSED" : implode(", ", $issues));
        
        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'total_records' => $this->db->table('pelanggan')->countAllResults()
        ];
    }

    /**
     * Validate kurir data
     */
    private function validateKurirData(): array
    {
        $issues = [];
        
        // Check for empty names
        $emptyNames = $this->db->query("
            SELECT COUNT(*) as count 
            FROM kurir 
            WHERE nama IS NULL OR nama = '' OR TRIM(nama) = ''
        ")->getRow()->count;
        
        if ($emptyNames > 0) {
            $issues[] = "Found {$emptyNames} kurir records with empty names";
        }

        // Check for invalid gender values
        $invalidGender = $this->db->query("
            SELECT COUNT(*) as count 
            FROM kurir 
            WHERE jenis_kelamin NOT IN ('Laki-Laki', 'Perempuan')
        ")->getRow()->count;
        
        if ($invalidGender > 0) {
            $issues[] = "Found {$invalidGender} kurir records with invalid gender values";
        }

        // Check for invalid phone numbers
        $invalidPhones = $this->db->query("
            SELECT COUNT(*) as count 
            FROM kurir 
            WHERE telepon IS NULL OR telepon = '' OR LENGTH(TRIM(telepon)) < 8
        ")->getRow()->count;
        
        if ($invalidPhones > 0) {
            $issues[] = "Found {$invalidPhones} kurir records with invalid phone numbers";
        }

        // Check ID format (should be KRR followed by numbers)
        $invalidIds = $this->db->query("
            SELECT COUNT(*) as count 
            FROM kurir 
            WHERE id_kurir NOT REGEXP '^KRR[0-9]+$'
        ")->getRow()->count;
        
        if ($invalidIds > 0) {
            $issues[] = "Found {$invalidIds} kurir records with invalid ID format";
        }

        $this->validationLog[] = "Kurir validation: " . (empty($issues) ? "PASSED" : implode(", ", $issues));
        
        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'total_records' => $this->db->table('kurir')->countAllResults()
        ];
    }

    /**
     * Validate pengiriman data
     */
    private function validatePengirimanData(): array
    {
        $issues = [];
        
        // Check for invalid pelanggan references
        $invalidPelanggan = $this->db->query("
            SELECT COUNT(*) as count 
            FROM pengiriman p 
            LEFT JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan 
            WHERE pel.id_pelanggan IS NULL
        ")->getRow()->count;
        
        if ($invalidPelanggan > 0) {
            $issues[] = "Found {$invalidPelanggan} pengiriman records with invalid pelanggan references";
        }

        // Check for invalid kurir references
        $invalidKurir = $this->db->query("
            SELECT COUNT(*) as count 
            FROM pengiriman p 
            LEFT JOIN kurir k ON p.id_kurir = k.id_kurir 
            WHERE k.id_kurir IS NULL
        ")->getRow()->count;
        
        if ($invalidKurir > 0) {
            $issues[] = "Found {$invalidKurir} pengiriman records with invalid kurir references";
        }

        // Check for invalid dates
        $invalidDates = $this->db->query("
            SELECT COUNT(*) as count 
            FROM pengiriman 
            WHERE tanggal IS NULL OR tanggal = '0000-00-00' OR tanggal > CURDATE()
        ")->getRow()->count;
        
        if ($invalidDates > 0) {
            $issues[] = "Found {$invalidDates} pengiriman records with invalid dates";
        }

        // Check ID format (should be KRM followed by date and numbers)
        $invalidIds = $this->db->query("
            SELECT COUNT(*) as count 
            FROM pengiriman 
            WHERE id_pengiriman NOT REGEXP '^KRM[0-9]{8}[0-9]+$'
        ")->getRow()->count;
        
        if ($invalidIds > 0) {
            $issues[] = "Found {$invalidIds} pengiriman records with invalid ID format";
        }

        $this->validationLog[] = "Pengiriman validation: " . (empty($issues) ? "PASSED" : implode(", ", $issues));
        
        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'total_records' => $this->db->table('pengiriman')->countAllResults()
        ];
    }

    /**
     * Validate detail pengiriman data
     */
    private function validateDetailPengirimanData(): array
    {
        $issues = [];
        
        // Check for invalid pengiriman references
        $invalidPengiriman = $this->db->query("
            SELECT COUNT(*) as count 
            FROM detail_pengiriman dp 
            LEFT JOIN pengiriman p ON dp.id_pengiriman = p.id_pengiriman 
            WHERE p.id_pengiriman IS NULL
        ")->getRow()->count;
        
        if ($invalidPengiriman > 0) {
            $issues[] = "Found {$invalidPengiriman} detail records with invalid pengiriman references";
        }

        // Check for invalid barang references
        $invalidBarang = $this->db->query("
            SELECT COUNT(*) as count 
            FROM detail_pengiriman dp 
            LEFT JOIN barang b ON dp.id_barang = b.id_barang 
            WHERE b.id_barang IS NULL
        ")->getRow()->count;
        
        if ($invalidBarang > 0) {
            $issues[] = "Found {$invalidBarang} detail records with invalid barang references";
        }

        // Check for invalid quantities
        $invalidQty = $this->db->query("
            SELECT COUNT(*) as count 
            FROM detail_pengiriman 
            WHERE qty IS NULL OR qty <= 0
        ")->getRow()->count;
        
        if ($invalidQty > 0) {
            $issues[] = "Found {$invalidQty} detail records with invalid quantities";
        }

        $this->validationLog[] = "Detail Pengiriman validation: " . (empty($issues) ? "PASSED" : implode(", ", $issues));
        
        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'total_records' => $this->db->table('detail_pengiriman')->countAllResults()
        ];
    }

    /**
     * Validate relationships between tables
     */
    private function validateRelationships(): array
    {
        $issues = [];
        
        // Check for pengiriman without details
        $pengirimanWithoutDetails = $this->db->query("
            SELECT COUNT(*) as count 
            FROM pengiriman p 
            LEFT JOIN detail_pengiriman dp ON p.id_pengiriman = dp.id_pengiriman 
            WHERE dp.id_pengiriman IS NULL
        ")->getRow()->count;
        
        if ($pengirimanWithoutDetails > 0) {
            $issues[] = "Found {$pengirimanWithoutDetails} pengiriman records without details";
        }

        // Check for kategori without barang
        $kategoriWithoutBarang = $this->db->query("
            SELECT COUNT(*) as count 
            FROM kategori k 
            LEFT JOIN barang b ON k.id_kategori = b.id_kategori 
            WHERE b.id_kategori IS NULL
        ")->getRow()->count;
        
        if ($kategoriWithoutBarang > 0) {
            $issues[] = "Found {$kategoriWithoutBarang} kategori records without barang";
        }

        $this->validationLog[] = "Relationship validation: " . (empty($issues) ? "PASSED" : implode(", ", $issues));
        
        return [
            'valid' => empty($issues),
            'issues' => $issues
        ];
    }

    /**
     * Remove duplicate records
     */
    private function removeDuplicateRecords(): array
    {
        $removed = [];
        
        // Remove duplicate kategori (keep the first one)
        $duplicateKategori = $this->db->query("
            DELETE k1 FROM kategori k1
            INNER JOIN kategori k2 
            WHERE k1.id_kategori = k2.id_kategori 
            AND k1.nama = k2.nama 
            AND k1.created_at > k2.created_at
        ");
        $removed['kategori'] = $this->db->affectedRows();

        // Remove duplicate barang
        $duplicateBarang = $this->db->query("
            DELETE b1 FROM barang b1
            INNER JOIN barang b2 
            WHERE b1.id_barang = b2.id_barang 
            AND b1.nama = b2.nama 
            AND b1.created_at > b2.created_at
        ");
        $removed['barang'] = $this->db->affectedRows();

        // Remove duplicate pelanggan
        $duplicatePelanggan = $this->db->query("
            DELETE p1 FROM pelanggan p1
            INNER JOIN pelanggan p2 
            WHERE p1.id_pelanggan = p2.id_pelanggan 
            AND p1.nama = p2.nama 
            AND p1.created_at > p2.created_at
        ");
        $removed['pelanggan'] = $this->db->affectedRows();

        // Remove duplicate kurir
        $duplicateKurir = $this->db->query("
            DELETE k1 FROM kurir k1
            INNER JOIN kurir k2 
            WHERE k1.id_kurir = k2.id_kurir 
            AND k1.nama = k2.nama 
            AND k1.created_at > k2.created_at
        ");
        $removed['kurir'] = $this->db->affectedRows();

        $totalRemoved = array_sum($removed);
        $this->cleanupLog[] = "Removed {$totalRemoved} duplicate records";
        
        return $removed;
    }

    /**
     * Clean up invalid records
     */
    private function cleanupInvalidRecords(): array
    {
        $cleaned = [];
        
        // Fix empty names in kategori
        $this->db->query("
            UPDATE kategori 
            SET nama = CONCAT('Kategori ', SUBSTRING(id_kategori, 4)) 
            WHERE nama IS NULL OR nama = '' OR TRIM(nama) = ''
        ");
        $cleaned['kategori_names'] = $this->db->affectedRows();

        // Fix empty names in barang
        $this->db->query("
            UPDATE barang 
            SET nama = CONCAT('Barang ', SUBSTRING(id_barang, 4)) 
            WHERE nama IS NULL OR nama = '' OR TRIM(nama) = ''
        ");
        $cleaned['barang_names'] = $this->db->affectedRows();

        // Fix empty satuan in barang
        $this->db->query("
            UPDATE barang 
            SET satuan = 'PCS' 
            WHERE satuan IS NULL OR satuan = '' OR TRIM(satuan) = ''
        ");
        $cleaned['barang_satuan'] = $this->db->affectedRows();

        // Fix invalid gender in kurir
        $this->db->query("
            UPDATE kurir 
            SET jenis_kelamin = 'Laki-Laki' 
            WHERE jenis_kelamin NOT IN ('Laki-Laki', 'Perempuan')
        ");
        $cleaned['kurir_gender'] = $this->db->affectedRows();

        // Fix invalid quantities in detail_pengiriman
        $this->db->query("
            UPDATE detail_pengiriman 
            SET qty = 1 
            WHERE qty IS NULL OR qty <= 0
        ");
        $cleaned['detail_qty'] = $this->db->affectedRows();

        $totalCleaned = array_sum($cleaned);
        $this->cleanupLog[] = "Cleaned {$totalCleaned} invalid records";
        
        return $cleaned;
    }

    /**
     * Clean up orphaned records
     */
    private function cleanupOrphanedRecords(): array
    {
        $removed = [];
        
        // Remove barang with invalid kategori references
        $this->db->query("
            DELETE b FROM barang b 
            LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
            WHERE k.id_kategori IS NULL
        ");
        $removed['orphaned_barang'] = $this->db->affectedRows();

        // Remove pengiriman with invalid references
        $this->db->query("
            DELETE p FROM pengiriman p 
            LEFT JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan 
            LEFT JOIN kurir k ON p.id_kurir = k.id_kurir 
            WHERE pel.id_pelanggan IS NULL OR k.id_kurir IS NULL
        ");
        $removed['orphaned_pengiriman'] = $this->db->affectedRows();

        // Remove detail_pengiriman with invalid references
        $this->db->query("
            DELETE dp FROM detail_pengiriman dp 
            LEFT JOIN pengiriman p ON dp.id_pengiriman = p.id_pengiriman 
            LEFT JOIN barang b ON dp.id_barang = b.id_barang 
            WHERE p.id_pengiriman IS NULL OR b.id_barang IS NULL
        ");
        $removed['orphaned_details'] = $this->db->affectedRows();

        $totalRemoved = array_sum($removed);
        $this->cleanupLog[] = "Removed {$totalRemoved} orphaned records";
        
        return $removed;
    }

    /**
     * Normalize data formats
     */
    private function normalizeData(): array
    {
        $normalized = [];
        
        // Normalize phone numbers (remove spaces, dashes)
        $this->db->query("
            UPDATE pelanggan 
            SET telepon = REPLACE(REPLACE(REPLACE(telepon, ' ', ''), '-', ''), '(', '')
        ");
        $this->db->query("
            UPDATE pelanggan 
            SET telepon = REPLACE(telepon, ')', '')
        ");
        $normalized['pelanggan_phones'] = $this->db->affectedRows();

        $this->db->query("
            UPDATE kurir 
            SET telepon = REPLACE(REPLACE(REPLACE(telepon, ' ', ''), '-', ''), '(', '')
        ");
        $this->db->query("
            UPDATE kurir 
            SET telepon = REPLACE(telepon, ')', '')
        ");
        $normalized['kurir_phones'] = $this->db->affectedRows();

        // Normalize names (trim whitespace, proper case)
        $this->db->query("
            UPDATE kategori 
            SET nama = TRIM(nama)
        ");
        $normalized['kategori_names'] = $this->db->affectedRows();

        $this->db->query("
            UPDATE barang 
            SET nama = TRIM(nama)
        ");
        $normalized['barang_names'] = $this->db->affectedRows();

        $this->db->query("
            UPDATE pelanggan 
            SET nama = TRIM(nama), alamat = TRIM(alamat)
        ");
        $normalized['pelanggan_data'] = $this->db->affectedRows();

        $this->db->query("
            UPDATE kurir 
            SET nama = TRIM(nama), alamat = TRIM(COALESCE(alamat, ''))
        ");
        $normalized['kurir_data'] = $this->db->affectedRows();

        $totalNormalized = array_sum($normalized);
        $this->cleanupLog[] = "Normalized {$totalNormalized} data records";
        
        return $normalized;
    }

    /**
     * Check if overall validation passed
     */
    private function isOverallValid(array $results): bool
    {
        foreach ($results as $key => $result) {
            if ($key !== 'validation_log' && isset($result['valid']) && !$result['valid']) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get validation log
     */
    public function getValidationLog(): array
    {
        return $this->validationLog;
    }

    /**
     * Get cleanup log
     */
    public function getCleanupLog(): array
    {
        return $this->cleanupLog;
    }

    /**
     * Generate data quality report
     */
    public function generateDataQualityReport(): array
    {
        $validation = $this->validateAllData();
        $cleanup = $this->cleanupInvalidData();
        
        return [
            'validation_results' => $validation,
            'cleanup_results' => $cleanup,
            'final_validation' => $this->validateAllData(),
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }
}