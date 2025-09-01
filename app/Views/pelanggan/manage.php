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
                    <div class="card-tools">
                        <a href="<?= base_url('pelanggan') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <form action="<?= base_url('pelanggan/save') ?>" method="post">
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

                        <!-- Hidden Fields -->
                        <input type="hidden" name="id" value="<?= $pelanggan->id_pelanggan ?? '' ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <!-- ID Pelanggan -->
                                <div class="mb-3">
                                    <label for="id_pelanggan" class="form-label">ID Pelanggan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="id_pelanggan" 
                                               name="id_pelanggan" 
                                               value="<?= old('id_pelanggan', $pelanggan->id_pelanggan ?? $autocode) ?>"
                                               <?= $isEdit ? 'readonly' : '' ?>
                                               required>
                                        <?php if (!$isEdit): ?>
                                        <button type="button" class="btn btn-outline-secondary" id="generateCode">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-text">Format: CSTxxxx (contoh: CST0001)</div>
                                </div>

                                <!-- Nama Pelanggan -->
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama" 
                                           name="nama" 
                                           value="<?= old('nama', $pelanggan->nama ?? '') ?>"
                                           maxlength="30"
                                           required>
                                    <div class="form-text">Maksimal 30 karakter</div>
                                </div>

                                <!-- Telepon -->
                                <div class="mb-3">
                                    <label for="telepon" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="telepon" 
                                           name="telepon" 
                                           value="<?= old('telepon', $pelanggan->telepon ?? '') ?>"
                                           maxlength="15"
                                           placeholder="contoh: 021-4603550"
                                           required>
                                    <div class="form-text">Maksimal 15 karakter</div>
                                    <div id="phone-validation" class="form-text"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Alamat -->
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control" 
                                              id="alamat" 
                                              name="alamat" 
                                              rows="6"
                                              maxlength="150"
                                              required><?= old('alamat', $pelanggan->alamat ?? '') ?></textarea>
                                    <div class="form-text">Maksimal 150 karakter</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                            <a href="<?= base_url('pelanggan') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate Code Button
    const generateCodeBtn = document.getElementById('generateCode');
    if (generateCodeBtn) {
        generateCodeBtn.addEventListener('click', function() {
            fetch('<?= base_url('pelanggan/generate-code') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('id_pelanggan').value = data.code;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }

    // Phone number validation
    const teleponField = document.getElementById('telepon');
    const phoneValidation = document.getElementById('phone-validation');
    
    if (teleponField && phoneValidation) {
        teleponField.addEventListener('input', function() {
            const phone = this.value;
            
            if (phone.length >= 10) {
                fetch('<?= base_url('pelanggan/validate-phone') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'telepon=' + encodeURIComponent(phone)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.valid) {
                            phoneValidation.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> ' + data.message + '</span>';
                            phoneValidation.innerHTML += '<br><small>Format: ' + data.formatted + '</small>';
                        } else {
                            phoneValidation.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> ' + data.message + '</span>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } else {
                phoneValidation.innerHTML = '';
            }
        });

        // Format phone number on blur
        teleponField.addEventListener('blur', function() {
            // Remove non-numeric characters except + and -
            this.value = this.value.replace(/[^\d+\-\s()]/g, '');
        });
    }

    // Character counter for nama field
    const namaField = document.getElementById('nama');
    if (namaField) {
        const maxLength = 30;
        const counter = document.createElement('div');
        counter.className = 'form-text text-end';
        counter.style.fontSize = '0.8em';
        namaField.parentNode.appendChild(counter);

        function updateNamaCounter() {
            const remaining = maxLength - namaField.value.length;
            counter.textContent = `${namaField.value.length}/${maxLength} karakter`;
            counter.className = remaining < 5 ? 'form-text text-end text-warning' : 'form-text text-end text-muted';
        }

        namaField.addEventListener('input', updateNamaCounter);
        updateNamaCounter();
    }

    // Character counter for alamat field
    const alamatField = document.getElementById('alamat');
    if (alamatField) {
        const maxLength = 150;
        const counter = document.createElement('div');
        counter.className = 'form-text text-end';
        counter.style.fontSize = '0.8em';
        alamatField.parentNode.appendChild(counter);

        function updateAlamatCounter() {
            const remaining = maxLength - alamatField.value.length;
            counter.textContent = `${alamatField.value.length}/${maxLength} karakter`;
            counter.className = remaining < 20 ? 'form-text text-end text-warning' : 'form-text text-end text-muted';
        }

        alamatField.addEventListener('input', updateAlamatCounter);
        updateAlamatCounter();
    }
});
</script>
<?= $this->endSection() ?>