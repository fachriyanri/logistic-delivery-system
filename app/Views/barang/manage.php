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
                        <a href="<?= base_url('barang') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <form action="<?= base_url('barang/save') ?>" method="post">
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
                        <input type="hidden" name="id" value="<?= $barang->id_barang ?? '' ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <!-- ID Barang -->
                                <div class="mb-3">
                                    <label for="id_barang" class="form-label">ID Barang <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="id_barang" 
                                               name="id_barang" 
                                               value="<?= old('id_barang', $barang->id_barang ?? $autocode) ?>"
                                               <?= $isEdit ? 'readonly' : '' ?>
                                               required>
                                        <?php if (!$isEdit): ?>
                                        <button type="button" class="btn btn-outline-secondary" id="generateCode">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-text">Format: BRGxxxx (contoh: BRG0001)</div>
                                </div>

                                <!-- Nama Barang -->
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama" 
                                           name="nama" 
                                           value="<?= old('nama', $barang->nama ?? '') ?>"
                                           maxlength="30"
                                           required>
                                    <div class="form-text">Maksimal 30 karakter</div>
                                </div>

                                <!-- Kategori -->
                                <div class="mb-3">
                                    <label for="id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select" id="id_kategori" name="id_kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach ($categories as $id => $nama): ?>
                                        <option value="<?= esc($id) ?>" 
                                                <?= old('id_kategori', $barang->id_kategori ?? '') === $id ? 'selected' : '' ?>>
                                            <?= esc($nama) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Satuan -->
                                <div class="mb-3">
                                    <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="satuan" 
                                           name="satuan" 
                                           value="<?= old('satuan', $barang->satuan ?? '') ?>"
                                           maxlength="20"
                                           placeholder="contoh: PCS, BOX, PALLET"
                                           required>
                                    <div class="form-text">Maksimal 20 karakter</div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" name="action" value="save_and_close" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                            <a href="<?= base_url('barang') ?>" class="btn btn-secondary">
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
            fetch('<?= base_url('barang/generate-code') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('id_barang').value = data.code;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
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

    // Character counter for satuan field
    const satuanField = document.getElementById('satuan');
    if (satuanField) {
        const maxLength = 20;
        const counter = document.createElement('div');
        counter.className = 'form-text text-end';
        counter.style.fontSize = '0.8em';
        satuanField.parentNode.appendChild(counter);

        function updateSatuanCounter() {
            const remaining = maxLength - satuanField.value.length;
            counter.textContent = `${satuanField.value.length}/${maxLength} karakter`;
            counter.className = remaining < 3 ? 'form-text text-end text-warning' : 'form-text text-end text-muted';
        }

        satuanField.addEventListener('input', updateSatuanCounter);
        updateSatuanCounter();
    }


});
</script>
<?= $this->endSection() ?>