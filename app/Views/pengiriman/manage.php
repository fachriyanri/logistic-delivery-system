<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= isset($pengiriman) ? 'Edit' : 'Tambah' ?> Pengiriman<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><?= isset($pengiriman) ? 'Edit' : 'Tambah' ?> Pengiriman</h1>
            <p class="mb-0 text-muted">Form untuk <?= isset($pengiriman) ? 'mengedit' : 'menambah' ?> data pengiriman</p>
        </div>
        <a href="<?= base_url('pengiriman') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Flash Messages -->
    <?= $this->include('layouts/partials/flash_messages') ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Main Form Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pengiriman</h6>
                </div>
                <div class="card-body">
                    <form action="<?= isset($pengiriman) ? base_url('pengiriman/update/' . $pengiriman->id_pengiriman) : base_url('pengiriman/store') ?>" 
                          method="POST" enctype="multipart/form-data" id="pengirimanForm">
                        <?= csrf_field() ?>
                        <?php if (isset($pengiriman)): ?>
                            <input type="hidden" name="_method" value="PUT">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_pengiriman" class="form-label">ID Pengiriman <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($validation) && $validation->hasError('id_pengiriman') ? 'is-invalid' : '' ?>" 
                                           id="id_pengiriman" name="id_pengiriman" 
                                           value="<?= old('id_pengiriman', $pengiriman->id_pengiriman ?? '') ?>"
                                           <?= isset($pengiriman) ? 'readonly' : '' ?> required>
                                    <?php if (isset($validation) && $validation->hasError('id_pengiriman')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('id_pengiriman') ?></div>
                                    <?php endif; ?>
                                    <?php if (!isset($pengiriman)): ?>
                                        <div class="form-text">ID akan dibuat otomatis jika dikosongkan</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control <?= isset($validation) && $validation->hasError('tanggal') ? 'is-invalid' : '' ?>" 
                                           id="tanggal" name="tanggal" 
                                           value="<?= old('tanggal', isset($pengiriman) ? date('Y-m-d', strtotime($pengiriman->tanggal)) : date('Y-m-d')) ?>" required>
                                    <?php if (isset($validation) && $validation->hasError('tanggal')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('tanggal') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_pelanggan" class="form-label">Pelanggan <span class="text-danger">*</span></label>
                                    <select class="form-select <?= isset($validation) && $validation->hasError('id_pelanggan') ? 'is-invalid' : '' ?>" 
                                            id="id_pelanggan" name="id_pelanggan" required>
                                        <option value="">Pilih Pelanggan</option>
                                        <?php foreach ($pelanggan as $p): ?>
                                            <option value="<?= $p->id_pelanggan ?>" 
                                                    <?= old('id_pelanggan', $pengiriman->id_pelanggan ?? '') == $p->id_pelanggan ? 'selected' : '' ?>>
                                                <?= esc($p->nama_pelanggan) ?> - <?= esc($p->alamat) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($validation) && $validation->hasError('id_pelanggan')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('id_pelanggan') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_kurir" class="form-label">Kurir <span class="text-danger">*</span></label>
                                    <select class="form-select <?= isset($validation) && $validation->hasError('id_kurir') ? 'is-invalid' : '' ?>" 
                                            id="id_kurir" name="id_kurir" required>
                                        <option value="">Pilih Kurir</option>
                                        <?php foreach ($kurir as $k): ?>
                                            <option value="<?= $k->id_kurir ?>" 
                                                    <?= old('id_kurir', $pengiriman->id_kurir ?? '') == $k->id_kurir ? 'selected' : '' ?>>
                                                <?= esc($k->nama_kurir) ?> - <?= esc($k->telepon) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($validation) && $validation->hasError('id_kurir')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('id_kurir') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_kendaraan" class="form-label">No Kendaraan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($validation) && $validation->hasError('no_kendaraan') ? 'is-invalid' : '' ?>" 
                                           id="no_kendaraan" name="no_kendaraan" 
                                           value="<?= old('no_kendaraan', $pengiriman->no_kendaraan ?? '') ?>" 
                                           placeholder="Contoh: B 1234 ABC" required>
                                    <?php if (isset($validation) && $validation->hasError('no_kendaraan')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('no_kendaraan') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_po" class="form-label">No PO <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($validation) && $validation->hasError('no_po') ? 'is-invalid' : '' ?>" 
                                           id="no_po" name="no_po" 
                                           value="<?= old('no_po', $pengiriman->no_po ?? '') ?>" 
                                           placeholder="Nomor Purchase Order" required>
                                    <?php if (isset($validation) && $validation->hasError('no_po')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('no_po') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="penerima" class="form-label">Penerima <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($validation) && $validation->hasError('penerima') ? 'is-invalid' : '' ?>" 
                                           id="penerima" name="penerima" 
                                           value="<?= old('penerima', $pengiriman->penerima ?? '') ?>" 
                                           placeholder="Nama penerima barang" required>
                                    <?php if (isset($validation) && $validation->hasError('penerima')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('penerima') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="1" <?= old('status', $pengiriman->status ?? '1') == '1' ? 'selected' : '' ?>>Pending</option>
                                        <option value="2" <?= old('status', $pengiriman->status ?? '') == '2' ? 'selected' : '' ?>>Dalam Perjalanan</option>
                                        <option value="3" <?= old('status', $pengiriman->status ?? '') == '3' ? 'selected' : '' ?>>Terkirim</option>
                                        <option value="4" <?= old('status', $pengiriman->status ?? '') == '4' ? 'selected' : '' ?>>Dibatalkan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                      placeholder="Keterangan tambahan (opsional)"><?= old('keterangan', $pengiriman->keterangan ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Foto Bukti Pengiriman</label>
                            <input type="file" class="form-control <?= isset($validation) && $validation->hasError('photo') ? 'is-invalid' : '' ?>" 
                                   id="photo" name="photo" accept="image/*">
                            <?php if (isset($validation) && $validation->hasError('photo')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('photo') ?></div>
                            <?php endif; ?>
                            <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB</div>
                            
                            <?php if (isset($pengiriman) && !empty($pengiriman->photo)): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Foto saat ini:</small><br>
                                    <img src="<?= base_url('uploads/pengiriman/' . $pengiriman->photo) ?>" 
                                         alt="Foto Pengiriman" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('pengiriman') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?= isset($pengiriman) ? 'Update' : 'Simpan' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Items Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Barang</h6>
                </div>
                <div class="card-body">
                    <div id="itemsContainer">
                        <?php if (isset($pengiriman) && !empty($detail_pengiriman)): ?>
                            <?php foreach ($detail_pengiriman as $index => $detail): ?>
                                <div class="item-row mb-3 p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">Item <?= $index + 1 ?></h6>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <select class="form-select form-select-sm" name="items[<?= $index ?>][id_barang]" required>
                                            <option value="">Pilih Barang</option>
                                            <?php foreach ($barang as $b): ?>
                                                <option value="<?= $b->id_barang ?>" 
                                                        <?= $detail->id_barang == $b->id_barang ? 'selected' : '' ?>>
                                                    <?= esc($b->nama_barang) ?> (<?= esc($b->satuan) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <input type="number" class="form-control form-control-sm" 
                                               name="items[<?= $index ?>][jumlah]" 
                                               value="<?= $detail->jumlah ?>"
                                               placeholder="Jumlah" min="1" required>
                                    </div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="items[<?= $index ?>][keterangan]" 
                                               value="<?= esc($detail->keterangan) ?>"
                                               placeholder="Keterangan (opsional)">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="item-row mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">Item 1</h6>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="mb-2">
                                    <select class="form-select form-select-sm" name="items[0][id_barang]" required>
                                        <option value="">Pilih Barang</option>
                                        <?php foreach ($barang as $b): ?>
                                            <option value="<?= $b->id_barang ?>">
                                                <?= esc($b->nama_barang) ?> (<?= esc($b->satuan) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <input type="number" class="form-control form-control-sm" 
                                           name="items[0][jumlah]" placeholder="Jumlah" min="1" required>
                                </div>
                                <div>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="items[0][keterangan]" placeholder="Keterangan (opsional)">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="button" class="btn btn-sm btn-outline-primary w-100" onclick="addItem()">
                        <i class="fas fa-plus"></i> Tambah Item
                    </button>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Bantuan</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <strong>Tips:</strong><br>
                        • ID Pengiriman akan dibuat otomatis jika dikosongkan<br>
                        • Pastikan semua field yang wajib (*) sudah diisi<br>
                        • Minimal harus ada 1 item barang<br>
                        • Foto bukti pengiriman bersifat opsional
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let itemIndex = <?= isset($detail_pengiriman) ? count($detail_pengiriman) : 1 ?>;

function addItem() {
    const container = document.getElementById('itemsContainer');
    const itemHtml = `
        <div class="item-row mb-3 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0">Item ${itemIndex + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-2">
                <select class="form-select form-select-sm" name="items[${itemIndex}][id_barang]" required>
                    <option value="">Pilih Barang</option>
                    <?php foreach ($barang as $b): ?>
                        <option value="<?= $b->id_barang ?>">
                            <?= esc($b->nama_barang) ?> (<?= esc($b->satuan) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-2">
                <input type="number" class="form-control form-control-sm" 
                       name="items[${itemIndex}][jumlah]" placeholder="Jumlah" min="1" required>
            </div>
            <div>
                <input type="text" class="form-control form-control-sm" 
                       name="items[${itemIndex}][keterangan]" placeholder="Keterangan (opsional)">
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
    updateItemNumbers();
}

function removeItem(button) {
    const itemRows = document.querySelectorAll('.item-row');
    if (itemRows.length > 1) {
        button.closest('.item-row').remove();
        updateItemNumbers();
    } else {
        alert('Minimal harus ada 1 item barang!');
    }
}

function updateItemNumbers() {
    const itemRows = document.querySelectorAll('.item-row');
    itemRows.forEach((row, index) => {
        const title = row.querySelector('h6');
        title.textContent = `Item ${index + 1}`;
    });
}

// Form validation
document.getElementById('pengirimanForm').addEventListener('submit', function(e) {
    const itemRows = document.querySelectorAll('.item-row');
    if (itemRows.length === 0) {
        e.preventDefault();
        alert('Minimal harus ada 1 item barang!');
        return false;
    }
    
    // Check if all required item fields are filled
    let isValid = true;
    itemRows.forEach(row => {
        const barangSelect = row.querySelector('select[name*="[id_barang]"]');
        const jumlahInput = row.querySelector('input[name*="[jumlah]"]');
        
        if (!barangSelect.value || !jumlahInput.value) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Pastikan semua item barang sudah dipilih dan jumlahnya sudah diisi!');
        return false;
    }
});

// Auto-generate ID if empty
document.getElementById('id_pengiriman').addEventListener('blur', function() {
    if (!this.value && !<?= isset($pengiriman) ? 'true' : 'false' ?>) {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        this.value = `SHP${year}${month}${day}${hours}${minutes}`;
    }
});
</script>
<?= $this->endSection() ?>