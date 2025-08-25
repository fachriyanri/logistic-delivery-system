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
                        <a href="<?= base_url('kurir') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <form action="<?= base_url('kurir/save') ?>" method="post">
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
                        <input type="hidden" name="id" value="<?= $kurir->id_kurir ?? '' ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <!-- ID Kurir -->
                                <div class="mb-3">
                                    <label for="id_kurir" class="form-label">ID Kurir <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="id_kurir" 
                                               name="id_kurir" 
                                               value="<?= old('id_kurir', $kurir->id_kurir ?? $autocode) ?>"
                                               <?= $isEdit ? 'readonly' : '' ?>
                                               required>
                                        <?php if (!$isEdit): ?>
                                        <button type="button" class="btn btn-outline-secondary" id="generateCode">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-text">Format: KRRxx (contoh: KRR01)</div>
                                </div>

                                <!-- Nama Kurir -->
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Kurir <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama" 
                                           name="nama" 
                                           value="<?= old('nama', $kurir->nama ?? '') ?>"
                                           maxlength="30"
                                           required>
                                    <div class="form-text">Maksimal 30 karakter</div>
                                </div>

                                <!-- Jenis Kelamin -->
                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <?php foreach ($genderOptions as $value => $label): ?>
                                        <option value="<?= esc($value) ?>" 
                                                <?= old('jenis_kelamin', $kurir->jenis_kelamin ?? '') === $value ? 'selected' : '' ?>>
                                            <?= esc($label) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Telepon -->
                                <div class="mb-3">
                                    <label for="telepon" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="telepon" 
                                           name="telepon" 
                                           value="<?= old('telepon', $kurir->telepon ?? '') ?>"
                                           maxlength="15"
                                           placeholder="contoh: 081234567890"
                                           required>
                                    <div class="form-text">Maksimal 15 karakter</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Alamat -->
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" 
                                              id="alamat" 
                                              name="alamat" 
                                              rows="4"
                                              maxlength="150"><?= old('alamat', $kurir->alamat ?? '') ?></textarea>
                                    <div class="form-text">Maksimal 150 karakter (opsional)</div>
                                </div>

                                <?php if (!$isEdit): ?>
                                <!-- Username (only for new kurir) -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="username" 
                                           name="username" 
                                           value="<?= old('username') ?>"
                                           minlength="3"
                                           maxlength="50"
                                           required>
                                    <div class="form-text">Username untuk login kurir, minimal 3 karakter</div>
                                </div>

                                <!-- Password (only for new kurir) -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               name="password" 
                                               minlength="6"
                                               required>
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Password untuk login kurir, minimal 6 karakter</div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" name="action" value="save" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                            <a href="<?= base_url('kurir') ?>" class="btn btn-secondary">
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
            fetch('<?= base_url('kurir/generate-code') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('id_kurir').value = data.code;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }

    // Toggle Password Visibility
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    
    if (togglePasswordBtn && passwordField) {
        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
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

    // Phone number formatting
    const teleponField = document.getElementById('telepon');
    if (teleponField) {
        teleponField.addEventListener('input', function() {
            // Remove non-numeric characters except + and -
            this.value = this.value.replace(/[^\d+\-]/g, '');
        });
    }
});
</script>
<?= $this->endSection() ?>