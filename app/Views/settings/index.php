<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
.settings-card {
    transition: all 0.2s ease-in-out;
}

.settings-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.setting-item {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.setting-item:last-child {
    border-bottom: none;
}

.setting-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.25rem;
}

.setting-description {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.database-stat {
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
}

.system-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.375rem;
    text-align: center;
}

.info-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.info-value {
    font-weight: 600;
    color: #495057;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .action-buttons {
        justify-content: center;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-1"><?= $title ?></h1>
                            <p class="text-muted mb-0">Kelola pengaturan sistem dan konfigurasi aplikasi</p>
                        </div>
                        <div class="action-buttons">
                            <button type="button" class="btn btn-outline-primary" onclick="refreshDatabaseStats()">
                                <i class="fas fa-sync-alt"></i> Refresh Data
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="submitAction('backup_settings')">
                                <i class="fas fa-download"></i> Backup Settings
                            </button>
                            <button type="button" class="btn btn-outline-warning" onclick="submitAction('clear_cache')">
                                <i class="fas fa-broom"></i> Clear Cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Settings Status Notice -->
            <?php if (!$settings_status['table_exists']): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-database me-2"></i>
                <strong>Settings Table Not Found!</strong>
                <p class="mb-2">The settings table doesn't exist yet. You need to run the database migration to create it.</p>
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <strong>Command Line:</strong> 
                        <code class="text-dark"><?= esc($settings_status['migration_command']) ?></code>
                    </div>
                    <div>
                        <strong>Or:</strong>
                        <a href="<?= base_url('settings/run-migration') ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-play me-1"></i> Run Migration Now
                        </a>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Settings Form -->
            <form id="settingsForm" action="<?= base_url('settings') ?>" method="post" <?= !$settings_status['table_exists'] ? 'style="pointer-events: none; opacity: 0.6;"' : '' ?>>
                <?= csrf_field() ?>
                <input type="hidden" name="action" id="actionField" value="save">
                
                <?php if (!$settings_status['table_exists']): ?>
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    The settings form is disabled until the settings table is created. Please run the migration first.
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Application Settings -->
                    <div class="col-lg-6 mb-4">
                        <div class="card settings-card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-cog me-2 text-primary"></i>
                                    Pengaturan Aplikasi
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <?php if (!empty($application_settings)): ?>
                                <?php foreach ($application_settings as $key => $setting): ?>
                                <div class="setting-item">
                                    <label for="<?= $key ?>" class="setting-label"><?= esc($setting['display_name']) ?></label>
                                    <?php if ($setting['description']): ?>
                                    <div class="setting-description"><?= esc($setting['description']) ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if ($setting['input_type'] === 'select' && $key === 'timezone'): ?>
                                    <select class="form-select" id="<?= $key ?>" name="<?= $key ?>" <?= !$setting['editable'] ? 'readonly' : '' ?>>
                                        <?php foreach ($timezone_options as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= $setting['value'] == $value ? 'selected' : '' ?>>
                                            <?= esc($label) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php else: ?>
                                    <input type="<?= $setting['input_type'] ?>" 
                                           class="form-control" 
                                           id="<?= $key ?>" 
                                           name="<?= $key ?>" 
                                           value="<?= esc($setting['value']) ?>"
                                           <?= !$setting['editable'] ? 'readonly' : '' ?>>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <div class="setting-item text-center text-muted">
                                    <i class="fas fa-info-circle mb-2"></i>
                                    <p class="mb-0">No application settings available. Please run migration first.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="col-lg-6 mb-4">
                        <div class="card settings-card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-building me-2 text-info"></i>
                                    Informasi Perusahaan
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <?php foreach ($company_settings as $key => $setting): ?>
                                <div class="setting-item">
                                    <label for="<?= $key ?>" class="setting-label"><?= esc($setting['display_name']) ?></label>
                                    <?php if ($setting['description']): ?>
                                    <div class="setting-description"><?= esc($setting['description']) ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if ($setting['input_type'] === 'textarea'): ?>
                                    <textarea class="form-control" 
                                              id="<?= $key ?>" 
                                              name="<?= $key ?>" 
                                              rows="2"
                                              <?= !$setting['editable'] ? 'readonly' : '' ?>><?= esc($setting['value']) ?></textarea>
                                    <?php else: ?>
                                    <input type="<?= $setting['input_type'] ?>" 
                                           class="form-control" 
                                           id="<?= $key ?>" 
                                           name="<?= $key ?>" 
                                           value="<?= esc($setting['value']) ?>"
                                           <?= !$setting['editable'] ? 'readonly' : '' ?>>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Display Settings -->
                    <div class="col-lg-6 mb-4">
                        <div class="card settings-card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-desktop me-2 text-success"></i>
                                    Pengaturan Tampilan
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <?php foreach ($display_settings as $key => $setting): ?>
                                <div class="setting-item">
                                    <label for="<?= $key ?>" class="setting-label"><?= esc($setting['display_name']) ?></label>
                                    <?php if ($setting['description']): ?>
                                    <div class="setting-description"><?= esc($setting['description']) ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if ($key === 'date_format'): ?>
                                    <select class="form-select" id="<?= $key ?>" name="<?= $key ?>" <?= !$setting['editable'] ? 'readonly' : '' ?>>
                                        <?php foreach ($date_format_options as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= $setting['value'] == $value ? 'selected' : '' ?>>
                                            <?= esc($label) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php else: ?>
                                    <input type="<?= $setting['input_type'] ?>" 
                                           class="form-control" 
                                           id="<?= $key ?>" 
                                           name="<?= $key ?>" 
                                           value="<?= esc($setting['value']) ?>"
                                           <?= !$setting['editable'] ? 'readonly' : '' ?>>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- System Settings -->
                    <div class="col-lg-6 mb-4">
                        <div class="card settings-card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-server me-2 text-warning"></i>
                                    Pengaturan Sistem
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <?php foreach ($system_settings as $key => $setting): ?>
                                <div class="setting-item">
                                    <label for="<?= $key ?>" class="setting-label"><?= esc($setting['display_name']) ?></label>
                                    <?php if ($setting['description']): ?>
                                    <div class="setting-description"><?= esc($setting['description']) ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if ($setting['input_type'] === 'checkbox'): ?>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="<?= $key ?>" 
                                               name="<?= $key ?>" 
                                               value="1"
                                               <?= $setting['value'] ? 'checked' : '' ?>
                                               <?= !$setting['editable'] ? 'disabled' : '' ?>>
                                        <label class="form-check-label" for="<?= $key ?>">
                                            <?= $setting['value'] ? 'Enabled' : 'Disabled' ?>
                                        </label>
                                    </div>
                                    <?php else: ?>
                                    <input type="<?= $setting['input_type'] ?>" 
                                           class="form-control" 
                                           id="<?= $key ?>" 
                                           name="<?= $key ?>" 
                                           value="<?= esc($setting['value']) ?>"
                                           <?= !$setting['editable'] ? 'readonly' : '' ?>>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Perubahan akan diterapkan segera setelah disimpan
                                        </p>
                                        <?php if (ENVIRONMENT === 'development'): ?>
                                        <small class="text-muted">
                                            Debug: <?= count($application_settings) ?> app settings, 
                                            <?= count($company_settings) ?> company settings loaded
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Database Information -->
            <div class="row mt-4">
                <div class="col-lg-6 mb-4">
                    <div class="card settings-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-database me-2 text-danger"></i>
                                Informasi Database
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="database-stat">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Status Koneksi:</strong>
                                    <span class="badge bg-<?= $database_stats['connection_status'] === 'Connected' ? 'success' : 'danger' ?> fs-6">
                                        <i class="fas fa-<?= $database_stats['connection_status'] === 'Connected' ? 'check' : 'times' ?>"></i>
                                        <?= esc($database_stats['connection_status']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="database-stat">
                                <div class="d-flex justify-content-between">
                                    <strong>Database Name:</strong>
                                    <span><?= esc($database_stats['database_name']) ?></span>
                                </div>
                            </div>
                            
                            <div class="database-stat">
                                <div class="d-flex justify-content-between">
                                    <strong>Total Tables:</strong>
                                    <span><?= esc($database_stats['total_tables']) ?> tables</span>
                                </div>
                            </div>
                            
                            <div class="database-stat">
                                <div class="d-flex justify-content-between">
                                    <strong>Database Size:</strong>
                                    <span><?= esc($database_stats['database_size']) ?></span>
                                </div>
                            </div>
                            
                            <?php if (!empty($database_stats['table_counts'])): ?>
                            <div class="mt-3">
                                <strong>Record Counts:</strong>
                                <div class="mt-2">
                                    <?php foreach ($database_stats['table_counts'] as $table => $count): ?>
                                    <div class="d-flex justify-content-between py-1">
                                        <span class="text-capitalize"><?= esc($table) ?>:</span>
                                        <span class="badge bg-secondary"><?= number_format($count) ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="submitAction('test_connection')">
                                    <i class="fas fa-plug"></i> Test Connection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="col-lg-6 mb-4">
                    <div class="card settings-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2 text-primary"></i>
                                Informasi Sistem
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="system-info-grid">
                                <div class="info-item">
                                    <div class="info-label">PHP Version</div>
                                    <div class="info-value"><?= esc($system_info['php_version']) ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">CodeIgniter</div>
                                    <div class="info-value"><?= esc($system_info['codeigniter_version']) ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Server</div>
                                    <div class="info-value"><?= esc($system_info['server_software']) ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Memory Limit</div>
                                    <div class="info-value"><?= esc($system_info['memory_limit']) ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Upload Limit</div>
                                    <div class="info-value"><?= esc($system_info['upload_max_filesize']) ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Timezone</div>
                                    <div class="info-value"><?= esc($system_info['timezone']) ?></div>
                                </div>
                            </div>
                            
                            <div class="mt-3 text-center">
                                <small class="text-muted">
                                    Last updated: <?= esc($system_info['current_time']) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function submitAction(action) {
    document.getElementById('actionField').value = action;
    
    // If saving, remove readonly fields before submission
    if (action === 'save') {
        const form = document.getElementById('settingsForm');
        const readonlyFields = form.querySelectorAll('input[readonly], select[readonly], input[disabled], select[disabled]');
        readonlyFields.forEach(function(field) {
            if (field.hasAttribute('name')) {
                field.setAttribute('data-original-name', field.getAttribute('name'));
                field.removeAttribute('name');
            }
        });
    }
    
    document.getElementById('settingsForm').submit();
}

function refreshDatabaseStats() {
    // Add loading indicator
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    btn.disabled = true;
    
    // Reload page to refresh data
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

// Auto-hide flash messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Add form validation
    const form = document.getElementById('settingsForm');
    form.addEventListener('submit', function(e) {
        const action = document.getElementById('actionField').value;
        
        if (action === 'save') {
            // Remove readonly and disabled fields from form submission
            const readonlyFields = form.querySelectorAll('input[readonly], select[readonly], input[disabled], select[disabled]');
            readonlyFields.forEach(function(field) {
                if (field.hasAttribute('name')) {
                    field.setAttribute('data-original-name', field.getAttribute('name'));
                    field.removeAttribute('name');
                }
            });
            
            // Debug: Log form data before submission (development only)
            <?php if (ENVIRONMENT === 'development'): ?>
            const formData = new FormData(form);
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            <?php endif; ?>
            
            // Add any custom validation here
            const requiredFields = form.querySelectorAll('input[required], select[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        }
    });
});
</script>
<?= $this->endSection() ?>