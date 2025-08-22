<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Migration Dashboard</h3>
                </div>
                <div class="card-body">
                    
                    <!-- Current Data Status -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Current Data Status</h5>
                            <div class="row">
                                <?php foreach ($dataStatus as $table => $count): ?>
                                <div class="col-md-2 col-sm-4 col-6 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4 class="text-primary"><?= number_format($count) ?></h4>
                                            <small class="text-muted"><?= ucfirst($table) ?></small>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Migration Options -->
                    <div class="row">
                        
                        <!-- Backup Tables Migration -->
                        <div class="col-md-4 mb-4">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Migrate from Backup Tables</h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Migrate data from tables with '_backup' suffix.</p>
                                    
                                    <?php if (!empty($backupTables)): ?>
                                        <div class="mb-3">
                                            <small class="text-muted">Available backup tables:</small>
                                            <ul class="list-unstyled mt-1">
                                                <?php foreach ($backupTables as $table => $count): ?>
                                                <li><small><?= $table ?>_backup: <?= $count ?> records</small></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="migrateFromBackup()">
                                            <i class="fas fa-database"></i> Migrate from Backup
                                        </button>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <small>No backup tables found</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Old Database Migration -->
                        <div class="col-md-4 mb-4">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Migrate from Old Database</h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Migrate data from an existing old database.</p>
                                    
                                    <div class="mb-3">
                                        <label for="oldDatabaseName" class="form-label">Database Name</label>
                                        <input type="text" class="form-control" id="oldDatabaseName" 
                                               placeholder="pengiriman_old" value="pengiriman">
                                    </div>
                                    
                                    <button type="button" class="btn btn-success" onclick="migrateFromOldDatabase()">
                                        <i class="fas fa-server"></i> Migrate from Database
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- SQL Dump Import -->
                        <div class="col-md-4 mb-4">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Import SQL Dump</h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Import data from an SQL dump file.</p>
                                    
                                    <div class="mb-3">
                                        <label for="sqlFile" class="form-label">SQL File</label>
                                        <input type="file" class="form-control" id="sqlFile" accept=".sql">
                                    </div>
                                    
                                    <button type="button" class="btn btn-info" onclick="importSQLDump()">
                                        <i class="fas fa-file-import"></i> Import SQL Dump
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Management -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">User Credential Management</h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Manage user accounts and update credentials for the new system.</p>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-danger me-2 mb-2" onclick="updateUserCredentials()">
                                                <i class="fas fa-key"></i> Update All Credentials
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-warning me-2 mb-2" onclick="createDefaultUsers()">
                                                <i class="fas fa-users"></i> Create Default Users
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-info me-2 mb-2" onclick="validateUserCredentials()">
                                                <i class="fas fa-user-check"></i> Validate Credentials
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info mt-3">
                                        <small>
                                            <strong>Default Users:</strong><br>
                                            • Admin: adminpuninar / AdminPuninar123<br>
                                            • Finance: financepuninar / FinancePuninar123<br>
                                            • Gudang: gudangpuninar / GudangPuninar123
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0">Data Verification & Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-warning me-2 mb-2" onclick="verifyIntegrity()">
                                                <i class="fas fa-check-circle"></i> Verify Data Integrity
                                            </button>
                                            <button type="button" class="btn btn-info me-2 mb-2" onclick="validateData()">
                                                <i class="fas fa-search"></i> Validate Data Quality
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-success me-2 mb-2" onclick="cleanupData()">
                                                <i class="fas fa-broom"></i> Cleanup Invalid Data
                                            </button>
                                            <button type="button" class="btn btn-secondary me-2 mb-2" onclick="refreshDataStatus()">
                                                <i class="fas fa-sync"></i> Refresh Status
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-primary" onclick="generateQualityReport()">
                                                <i class="fas fa-file-alt"></i> Generate Quality Report
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Migration Log -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Migration Log</h6>
                                </div>
                                <div class="card-body">
                                    <div id="migrationLog" class="bg-dark text-light p-3 rounded" style="height: 300px; overflow-y: auto; font-family: monospace;">
                                        <div class="text-muted">Migration log will appear here...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2">Processing migration...</div>
            </div>
        </div>
    </div>
</div>

<script>
function logMessage(message, type = 'info') {
    const log = document.getElementById('migrationLog');
    const timestamp = new Date().toLocaleTimeString();
    const colorClass = type === 'error' ? 'text-danger' : type === 'success' ? 'text-success' : 'text-info';
    
    log.innerHTML += `<div class="${colorClass}">[${timestamp}] ${message}</div>`;
    log.scrollTop = log.scrollHeight;
}

function showLoading() {
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
}

function hideLoading() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
    if (modal) modal.hide();
}

function migrateFromBackup() {
    showLoading();
    logMessage('Starting migration from backup tables...', 'info');
    
    fetch('<?= base_url('admin/data-migration/migrate-from-backup') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            logMessage('Migration completed successfully!', 'success');
            if (data.log) {
                data.log.forEach(entry => logMessage(entry, 'info'));
            }
            refreshDataStatus();
        } else {
            logMessage('Migration failed: ' + data.message, 'error');
            if (data.log) {
                data.log.forEach(entry => logMessage(entry, 'error'));
            }
        }
    })
    .catch(error => {
        hideLoading();
        logMessage('Error: ' + error.message, 'error');
    });
}

function migrateFromOldDatabase() {
    const databaseName = document.getElementById('oldDatabaseName').value;
    
    if (!databaseName) {
        logMessage('Please enter the old database name', 'error');
        return;
    }
    
    showLoading();
    logMessage(`Starting migration from database: ${databaseName}...`, 'info');
    
    fetch('<?= base_url('admin/data-migration/migrate-from-old-database') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `old_database=${encodeURIComponent(databaseName)}`
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            logMessage('Migration completed successfully!', 'success');
            if (data.log) {
                data.log.forEach(entry => logMessage(entry, 'info'));
            }
            refreshDataStatus();
        } else {
            logMessage('Migration failed: ' + data.message, 'error');
            if (data.log) {
                data.log.forEach(entry => logMessage(entry, 'error'));
            }
        }
    })
    .catch(error => {
        hideLoading();
        logMessage('Error: ' + error.message, 'error');
    });
}

function importSQLDump() {
    const fileInput = document.getElementById('sqlFile');
    const file = fileInput.files[0];
    
    if (!file) {
        logMessage('Please select an SQL file', 'error');
        return;
    }
    
    showLoading();
    logMessage(`Starting SQL import: ${file.name}...`, 'info');
    
    const formData = new FormData();
    formData.append('sql_file', file);
    
    fetch('<?= base_url('admin/data-migration/import-sql-dump') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            logMessage('SQL import completed successfully!', 'success');
            if (data.log) {
                data.log.forEach(entry => logMessage(entry, 'info'));
            }
            refreshDataStatus();
        } else {
            logMessage('SQL import failed: ' + data.message, 'error');
            if (data.log) {
                data.log.forEach(entry => logMessage(entry, 'error'));
            }
        }
    })
    .catch(error => {
        hideLoading();
        logMessage('Error: ' + error.message, 'error');
    });
}

function verifyIntegrity() {
    logMessage('Starting data integrity verification...', 'info');
    
    fetch('<?= base_url('admin/data-migration/verify-integrity') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const integrity = data.integrity;
            logMessage('Data integrity verification completed:', 'success');
            logMessage(`- Kategori: ${integrity.kategori_count} records`, 'info');
            logMessage(`- Barang: ${integrity.barang_count} records`, 'info');
            logMessage(`- Pelanggan: ${integrity.pelanggan_count} records`, 'info');
            logMessage(`- Kurir: ${integrity.kurir_count} records`, 'info');
            logMessage(`- Pengiriman: ${integrity.pengiriman_count} records`, 'info');
            logMessage(`- Detail Pengiriman: ${integrity.detail_pengiriman_count} records`, 'info');
            
            if (integrity.integrity_check) {
                logMessage('✓ All foreign key relationships are valid', 'success');
            } else {
                logMessage('✗ Data integrity issues found:', 'error');
                logMessage(`- Orphaned Barang: ${integrity.orphaned_barang}`, 'error');
                logMessage(`- Orphaned Pengiriman: ${integrity.orphaned_pengiriman}`, 'error');
                logMessage(`- Orphaned Details: ${integrity.orphaned_details}`, 'error');
            }
        } else {
            logMessage('Integrity verification failed: ' + data.message, 'error');
        }
    })
    .catch(error => {
        logMessage('Error: ' + error.message, 'error');
    });
}

function refreshDataStatus() {
    fetch('<?= base_url('admin/data-migration/get-data-status') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the status cards
            Object.keys(data.status).forEach(table => {
                const card = document.querySelector(`[data-table="${table}"]`);
                if (card) {
                    card.textContent = data.status[table].toLocaleString();
                }
            });
            logMessage('Data status refreshed', 'success');
        }
    })
    .catch(error => {
        logMessage('Error refreshing status: ' + error.message, 'error');
    });
}

function validateData() {
    logMessage('Starting comprehensive data validation...', 'info');
    
    fetch('<?= base_url('admin/data-migration/validate-data') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const validation = data.validation;
            logMessage('Data validation completed:', 'success');
            
            // Display validation results for each table
            Object.keys(validation).forEach(table => {
                if (table === 'validation_log' || table === 'overall_valid') return;
                
                const result = validation[table];
                const status = result.valid ? '✓' : '✗';
                const type = result.valid ? 'success' : 'error';
                
                logMessage(`${status} ${table}: ${result.total_records || 'N/A'} records`, type);
                
                if (result.issues && result.issues.length > 0) {
                    result.issues.forEach(issue => {
                        logMessage(`  - ${issue}`, 'error');
                    });
                }
            });
            
            if (validation.overall_valid) {
                logMessage('✓ All data validation checks passed!', 'success');
            } else {
                logMessage('⚠ Data validation found issues that need attention', 'error');
            }
        } else {
            logMessage('Data validation failed: ' + data.message, 'error');
        }
    })
    .catch(error => {
        logMessage('Error during validation: ' + error.message, 'error');
    });
}

function cleanupData() {
    if (!confirm('This will automatically fix common data issues. Continue?')) {
        return;
    }
    
    showLoading();
    logMessage('Starting data cleanup process...', 'info');
    
    fetch('<?= base_url('admin/data-migration/cleanup-data') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            const cleanup = data.cleanup;
            logMessage('Data cleanup completed successfully!', 'success');
            
            // Display cleanup results
            Object.keys(cleanup).forEach(category => {
                if (category === 'cleanup_log') return;
                
                const results = cleanup[category];
                logMessage(`${category.replace('_', ' ').toUpperCase()}:`, 'info');
                
                if (typeof results === 'object') {
                    Object.keys(results).forEach(type => {
                        if (results[type] > 0) {
                            logMessage(`  - ${type}: ${results[type]} records processed`, 'success');
                        }
                    });
                }
            });
            
            // Display cleanup log
            if (cleanup.cleanup_log) {
                cleanup.cleanup_log.forEach(entry => {
                    logMessage(entry, 'info');
                });
            }
            
            refreshDataStatus();
        } else {
            logMessage('Data cleanup failed: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        logMessage('Error during cleanup: ' + error.message, 'error');
    });
}

function generateQualityReport() {
    showLoading();
    logMessage('Generating comprehensive data quality report...', 'info');
    
    fetch('<?= base_url('admin/data-migration/generate-quality-report') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            logMessage('Data quality report generated successfully!', 'success');
            logMessage(`Report saved as: ${data.file}`, 'info');
            
            // Display summary of the report
            const report = data.report;
            if (report.validation_results) {
                const overallValid = report.validation_results.overall_valid;
                logMessage(`Overall validation status: ${overallValid ? 'PASSED' : 'FAILED'}`, 
                          overallValid ? 'success' : 'error');
            }
            
            if (report.final_validation) {
                const finalValid = report.final_validation.overall_valid;
                logMessage(`Final validation status: ${finalValid ? 'PASSED' : 'FAILED'}`, 
                          finalValid ? 'success' : 'error');
            }
        } else {
            logMessage('Report generation failed: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        logMessage('Error generating report: ' + error.message, 'error');
    });
}

function updateUserCredentials() {
    if (!confirm('This will update all user credentials and create default users. Continue?')) {
        return;
    }
    
    showLoading();
    logMessage('Starting complete user credential update...', 'info');
    
    fetch('<?= base_url('admin/data-migration/update-user-credentials') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            logMessage('User credential update completed successfully!', 'success');
            
            const result = data.result;
            
            // Display migration results
            if (result.migration) {
                logMessage('Migration Results:', 'info');
                result.migration.log.forEach(entry => logMessage(`  - ${entry}`, 'info'));
            }
            
            // Display credential update results
            if (result.credential_update) {
                logMessage(`Updated ${result.credential_update.updated_count} user passwords`, 'success');
                result.credential_update.log.forEach(entry => logMessage(`  - ${entry}`, 'info'));
            }
            
            // Display default users results
            if (result.default_users) {
                logMessage(`Created ${result.default_users.created_count} new users, updated ${result.default_users.updated_count} existing users`, 'success');
                result.default_users.log.forEach(entry => logMessage(`  - ${entry}`, 'info'));
            }
            
            // Display permission setup results
            if (result.permissions) {
                logMessage('Permission setup completed', 'success');
                result.permissions.log.forEach(entry => logMessage(`  - ${entry}`, 'info'));
            }
            
            // Display final validation
            if (result.validation) {
                const validation = result.validation;
                if (validation.valid) {
                    logMessage(`✓ All ${validation.total_users} user credentials are valid`, 'success');
                } else {
                    logMessage(`✗ User validation found issues:`, 'error');
                    validation.issues.forEach(issue => logMessage(`  - ${issue}`, 'error'));
                }
            }
            
        } else {
            logMessage('User credential update failed: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        logMessage('Error updating user credentials: ' + error.message, 'error');
    });
}

function createDefaultUsers() {
    showLoading();
    logMessage('Creating default user accounts...', 'info');
    
    fetch('<?= base_url('admin/data-migration/create-default-users') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            const result = data.result;
            logMessage('Default user creation completed!', 'success');
            logMessage(`Created ${result.created_count} new users, updated ${result.updated_count} existing users`, 'info');
            
            result.log.forEach(entry => logMessage(`  - ${entry}`, 'info'));
        } else {
            logMessage('Default user creation failed: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        logMessage('Error creating default users: ' + error.message, 'error');
    });
}

function validateUserCredentials() {
    logMessage('Validating user credentials...', 'info');
    
    fetch('<?= base_url('admin/data-migration/validate-user-credentials') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const validation = data.validation;
            
            if (validation.valid) {
                logMessage(`✓ All ${validation.total_users} user credentials are valid`, 'success');
            } else {
                logMessage(`✗ User credential validation found issues:`, 'error');
                logMessage(`Total users: ${validation.total_users}`, 'info');
                
                validation.issues.forEach(issue => {
                    logMessage(`  - ${issue}`, 'error');
                });
            }
        } else {
            logMessage('User credential validation failed: ' + data.message, 'error');
        }
    })
    .catch(error => {
        logMessage('Error validating user credentials: ' + error.message, 'error');
    });
}
</script>

<?= $this->endSection() ?>