<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $title ?></h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('kategori/save') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <?php if ($isEdit ?? false): ?>
                            <input type="hidden" name="original_id" value="<?= $kategori->id_kategori ?? '' ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_kategori" class="form-label">ID Kategori *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="id_kategori" 
                                           name="id_kategori" 
                                           value="<?= old('id_kategori', $kategori->id_kategori ?? $autocode) ?>"
                                           maxlength="5"
                                           pattern="KTG[0-9]{2}"
                                           <?= ($isEdit ?? false) ? 'readonly' : '' ?>
                                           required>
                                    <div class="form-text">Format: KTGxx (contoh: KTG01)</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Kategori *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama" 
                                           name="nama" 
                                           value="<?= old('nama', $kategori->nama ?? '') ?>"
                                           maxlength="30"
                                           required>
                                    <div class="form-text">Maksimal 30 karakter</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea class="form-control" 
                                              id="keterangan" 
                                              name="keterangan" 
                                              rows="4"
                                              maxlength="150"><?= old('keterangan', $kategori->keterangan ?? '') ?></textarea>
                                    <div class="form-text">Maksimal 150 karakter (opsional)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('kategori') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                <?= ($isEdit ?? false) ? 'Update' : 'Simpan' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const idField = document.getElementById('id_kategori');
    const namaField = document.getElementById('nama');
    
    // ID is auto-incremented by the system (KTG01, KTG02, etc.)
    // No need for auto-generation from category name
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let hasErrors = false;
        
        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        
        // Validate ID
        if (!idField.value.trim()) {
            showError(idField, 'ID Kategori harus diisi');
            hasErrors = true;
        } else if (!/^KTG[0-9]{2}$/.test(idField.value)) {
            showError(idField, 'Format ID harus KTGxx (contoh: KTG01)');
            hasErrors = true;
        }
        
        // Validate name
        if (!namaField.value.trim()) {
            showError(namaField, 'Nama kategori harus diisi');
            hasErrors = true;
        } else if (namaField.value.length < 3) {
            showError(namaField, 'Nama kategori minimal 3 karakter');
            hasErrors = true;
        }
        
        if (hasErrors) {
            e.preventDefault();
            return false;
        }
        
        // Show loading
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    });
    
    function showError(field, message) {
        field.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-danger mt-1';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }
});
</script>
<?= $this->endSection() ?>