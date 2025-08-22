<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\DataMigrationService;

class MigrateData extends BaseCommand
{
    protected $group       = 'Migration';
    protected $name        = 'migrate:data';
    protected $description = 'Migrate data from old CodeIgniter 2.2 structure to new CI4 structure';
    protected $usage       = 'migrate:data [options]';
    protected $arguments   = [];
    protected $options     = [
        '--source'    => 'Source type: old-db, backup-tables, or sql-dump',
        '--database'  => 'Old database name (for old-db source)',
        '--file'      => 'SQL dump file path (for sql-dump source)',
        '--verify'    => 'Verify data integrity after migration',
    ];

    public function run(array $params)
    {
        $source = CLI::getOption('source') ?? 'backup-tables';
        $database = CLI::getOption('database');
        $file = CLI::getOption('file');
        $verify = CLI::getOption('verify') !== null;

        CLI::write('Starting data migration...', 'green');
        CLI::newLine();

        $migrationService = new DataMigrationService();

        try {
            switch ($source) {
                case 'old-db':
                    if (!$database) {
                        CLI::error('Database name is required for old-db source. Use --database option.');
                        return;
                    }
                    CLI::write("Migrating from old database: {$database}");
                    $log = $migrationService->migrateFromOldDatabase($database);
                    break;

                case 'sql-dump':
                    if (!$file) {
                        CLI::error('File path is required for sql-dump source. Use --file option.');
                        return;
                    }
                    CLI::write("Importing from SQL dump: {$file}");
                    $log = $migrationService->importFromSQLDump($file);
                    break;

                case 'backup-tables':
                default:
                    CLI::write('Migrating from backup tables...');
                    $log = $migrationService->migrateFromBackupTables();
                    break;
            }

            // Display migration log
            CLI::newLine();
            CLI::write('Migration Log:', 'yellow');
            foreach ($log as $entry) {
                CLI::write("  - {$entry}");
            }

            if ($verify) {
                CLI::newLine();
                CLI::write('Verifying data integrity...', 'yellow');
                $integrity = $migrationService->verifyDataIntegrity();
                
                CLI::write("Records migrated:");
                CLI::write("  - Kategori: {$integrity['kategori_count']}");
                CLI::write("  - Barang: {$integrity['barang_count']}");
                CLI::write("  - Pelanggan: {$integrity['pelanggan_count']}");
                CLI::write("  - Kurir: {$integrity['kurir_count']}");
                CLI::write("  - Pengiriman: {$integrity['pengiriman_count']}");
                CLI::write("  - Detail Pengiriman: {$integrity['detail_pengiriman_count']}");
                
                CLI::newLine();
                if ($integrity['integrity_check']) {
                    CLI::write('✓ Data integrity check passed', 'green');
                } else {
                    CLI::write('✗ Data integrity issues found:', 'red');
                    CLI::write("  - Orphaned Barang: {$integrity['orphaned_barang']}");
                    CLI::write("  - Orphaned Pengiriman: {$integrity['orphaned_pengiriman']}");
                    CLI::write("  - Orphaned Details: {$integrity['orphaned_details']}");
                }
            }

            CLI::newLine();
            CLI::write('Data migration completed successfully!', 'green');

        } catch (\Exception $e) {
            CLI::error('Migration failed: ' . $e->getMessage());
            
            // Display any partial log
            $log = $migrationService->getMigrationLog();
            if (!empty($log)) {
                CLI::newLine();
                CLI::write('Partial migration log:', 'yellow');
                foreach ($log as $entry) {
                    CLI::write("  - {$entry}");
                }
            }
        }
    }
}