<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $title ?></h3>
                </div>
                
                <div class="card-body">
                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Application Settings -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Pengaturan Aplikasi</h5>
                                </div>
                                <div class="card-body">
                                    <form action="<?= base_url('settings') ?>" method="post">
                                        <div class="mb-3">
                                            <label for="app_name" class="form-label">Nama Aplikasi</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="app_name" 
                                                   name="app_name" 
                                                   value="PuninarLogistic"
                                                   readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label for="app_version" class="form-label">Versi Aplikasi</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="app_version" 
                                                   value="1.0.0"
                                                   readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label for="timezone" class="form-label">Zona Waktu</label>
                                            <select class="form-control" id="timezone" name="timezone">
                                                <option value="Asia/Jakarta" selected>Asia/Jakarta (WIB)</option>
                                                <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                                <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan Pengaturan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Database Settings -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Informasi Database</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Status Koneksi Database</label>
                                        <div>
                                            <span class="badge bg-success fs-6">
                                                <i class="fas fa-check"></i> Terhubung
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Database Name</label>
                                        <div>puninar_logistic</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Total Tables</label>
                                        <div>7 tables</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Database Size</label>
                                        <div>~2 MB</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Information -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Informasi Sistem</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>PHP Version:</strong><br>
                                            <?= phpversion() ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>CodeIgniter Version:</strong><br>
                                            <?= \CodeIgniter\CodeIgniter::CI_VERSION ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Server:</strong><br>
                                            <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Memory Limit:</strong><br>
                                            <?= ini_get('memory_limit') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>