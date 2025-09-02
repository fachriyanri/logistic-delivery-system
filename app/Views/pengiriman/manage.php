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
                        method="POST" id="pengirimanForm">
                        <?= csrf_field() ?>
                        <?php if (isset($pengiriman)): ?>
                            <input type="hidden" name="_method" value="PUT">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_pengiriman" class="form-label">ID Pengiriman</label>
                                    <input type="text" class="form-control"
                                        id="id_pengiriman" name="id_pengiriman"
                                        value="<?= old('id_pengiriman', $pengiriman->id_pengiriman ?? $autocode ?? '') ?>"
                                        readonly>
                                    <div class="form-text">ID Pengiriman dibuat secara otomatis.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control <?= isset($validation) && $validation->hasError('tanggal') ? 'is-invalid' : '' ?>"
                                        id="tanggal" name="tanggal"
                                        value="<?= old('tanggal', isset($pengiriman) ? date('Y-m-d', strtotime($pengiriman->tanggal)) : date('Y-m-d')) ?>" 
                                        <?= session('level') == 2 ? 'readonly' : '' ?> required>
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
                                        id="id_pelanggan" name="id_pelanggan" 
                                        <?= session('level') == 2 ? 'disabled' : '' ?> required>
                                        <option value="">Pilih Pelanggan</option>
                                        <?php foreach ($pelanggan as $p): ?>
                                            <option value="<?= $p->id_pelanggan ?>"
                                                <?= old('id_pelanggan', $pengiriman->id_pelanggan ?? '') == $p->id_pelanggan ? 'selected' : '' ?>>
                                                <?= esc($p->nama) ?> - <?= esc($p->alamat) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <!-- Hidden field to preserve value when disabled for level 2 -->
                                    <?php if (session('level') == 2 && isset($pengiriman)): ?>
                                        <input type="hidden" name="id_pelanggan" value="<?= $pengiriman->id_pelanggan ?>">
                                    <?php endif; ?>
                                    <?php if (isset($validation) && $validation->hasError('id_pelanggan')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('id_pelanggan') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_kurir" class="form-label">Kurir <span class="text-danger">*</span></label>
                                    <select class="form-select <?= isset($validation) && $validation->hasError('id_kurir') ? 'is-invalid' : '' ?>"
                                        id="id_kurir" name="id_kurir" 
                                        <?= session('level') == 2 ? 'disabled' : '' ?> required>
                                        <option value="">Pilih Kurir</option>
                                        <?php foreach ($kurir as $k): ?>
                                            <option value="<?= $k->id_kurir ?>"
                                                <?= old('id_kurir', $pengiriman->id_kurir ?? '') == $k->id_kurir ? 'selected' : '' ?>>
                                                <?= esc($k->nama) ?> - <?= esc($k->telepon) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <!-- Hidden field to preserve value when disabled for level 2 -->
                                    <?php if (session('level') == 2 && isset($pengiriman)): ?>
                                        <input type="hidden" name="id_kurir" value="<?= $pengiriman->id_kurir ?>">
                                    <?php endif; ?>
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
                                        placeholder="Contoh: B 1234 ABC" 
                                        <?= session('level') == 2 ? 'readonly' : '' ?> required>
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
                                        value="<?= old('no_po', $pengiriman->no_po ?? $autoPO ?? '') ?>"
                                        placeholder="Nomor Purchase Order" <?= isset($pengiriman) ? 'readonly' : 'readonly' ?> required>
                                    <?php if (isset($validation) && $validation->hasError('no_po')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('no_po') ?></div>
                                    <?php endif; ?>
                                    <?php if (!isset($pengiriman)): ?>
                                        <div class="form-text">No PO akan dibuat otomatis</div>
                                    <?php else: ?>
                                        <div class="form-text">No PO tidak dapat diubah saat mengedit pengiriman</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
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
                            <?php if (session('level') == 2): ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="detail_location" class="form-label">Detail Lokasi</label>
                                    <input type="text" class="form-control <?= isset($validation) && $validation->hasError('detail_location') ? 'is-invalid' : '' ?>"
                                        id="detail_location" name="detail_location"
                                        value="<?= old('detail_location', $pengiriman->detail_location ?? '') ?>"
                                        placeholder="Detail lokasi pengiriman">
                                    <?php if (isset($validation) && $validation->hasError('detail_location')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('detail_location') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if (session('level') == 2): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="penerima" class="form-label">Penerima</label>
                                    <input type="text" class="form-control <?= isset($validation) && $validation->hasError('penerima') ? 'is-invalid' : '' ?>"
                                        id="penerima" name="penerima"
                                        value="<?= old('penerima', $pengiriman->penerima ?? '') ?>"
                                        placeholder="Nama penerima barang (opsional)">
                                    <?php if (isset($validation) && $validation->hasError('penerima')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('penerima') ?></div>
                                    <?php endif; ?>
                                    <div class="form-text">Field ini opsional, isi jika barang sudah diterima</div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                                placeholder="Keterangan tambahan (opsional)" 
                                <?= session('level') == 2 ? 'readonly' : '' ?>><?= old('keterangan', $pengiriman->keterangan ?? '') ?></textarea>
                        </div>

                        <!-- Items Section -->
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-primary mb-3">Detail Barang</h6>
                            <?php if (session('level') == 2): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Detail barang tidak dapat diubah oleh kurir.
                                </div>
                            <?php endif; ?>
                            <div id="itemsContainer">
                                <?php if (isset($pengiriman) && !empty($detail_pengiriman)): ?>
                                    <?php foreach ($detail_pengiriman as $index => $detail): ?>
                                        <div class="item-row mb-3 p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">Item <?= $index + 1 ?></h6>
                                                <?php if (session('level') != 2): ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mb-2">
                                                <select class="form-select form-select-sm" name="items[<?= $index ?>][id_barang]" 
                                                    <?= session('level') == 2 ? 'disabled' : '' ?> required>
                                                    <option value="">Pilih Barang</option>
                                                    <?php foreach ($barang as $b): ?>
                                                        <option value="<?= $b->id_barang ?>"
                                                            <?= $detail->id_barang == $b->id_barang ? 'selected' : '' ?>>
                                                            <?= esc($b->nama) ?> (<?= esc($b->satuan) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if (session('level') == 2): ?>
                                                    <input type="hidden" name="items[<?= $index ?>][id_barang]" value="<?= $detail->id_barang ?>">
                                                <?php endif; ?>
                                            </div>
                                            <div class="mb-2">
                                                <input type="number" class="form-control form-control-sm"
                                                    name="items[<?= $index ?>][qty]"
                                                    value="<?= $detail->qty ?>"
                                                    placeholder="Jumlah" min="1" 
                                                    <?= session('level') == 2 ? 'readonly' : '' ?> required>
                                            </div>
                                            <div>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="items[<?= $index ?>][keterangan]"
                                                    value="<?= esc($detail->keterangan) ?>"
                                                    placeholder="Keterangan (opsional)" 
                                                    <?= session('level') == 2 ? 'readonly' : '' ?>>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="item-row mb-3 p-3 border rounded">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0">Item 1</h6>
                                            <?php if (session('level') != 2): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mb-2">
                                            <select class="form-select form-select-sm" name="items[0][id_barang]" 
                                                <?= session('level') == 2 ? 'disabled' : '' ?> required>
                                                <option value="">Pilih Barang</option>
                                                <?php foreach ($barang as $b): ?>
                                                    <option value="<?= $b->id_barang ?>">
                                                        <?= esc($b->nama) ?> (<?= esc($b->satuan) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <input type="number" class="form-control form-control-sm"
                                                name="items[0][qty]" placeholder="Jumlah" min="1" 
                                                <?= session('level') == 2 ? 'readonly' : '' ?> required>
                                        </div>
                                        <div>
                                            <input type="text" class="form-control form-control-sm"
                                                name="items[0][keterangan]" placeholder="Keterangan (opsional)" 
                                                <?= session('level') == 2 ? 'readonly' : '' ?>>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (session('level') != 2): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary w-100 mb-3" onclick="addItem()">
                                <i class="fas fa-plus"></i> Tambah Item
                            </button>
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
            <!-- Help Card -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Bantuan</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <strong>Tips:</strong><br>
                        <?php if (session('level') == 2): ?>
                        • Sebagai kurir, Anda hanya dapat mengubah status, detail lokasi, dan penerima<br>
                        • Field lain tidak dapat diubah dan bersifat informasi saja<br>
                        • Pastikan status pengiriman diperbarui sesuai kondisi terkini<br>
                        • Detail lokasi membantu pelacakan pengiriman<br>
                        • Penerima bersifat opsional - isi jika barang sudah diterima
                        <?php else: ?>
                        • ID Pengiriman akan dibuat otomatis jika dikosongkan<br>
                        • No PO akan dibuat otomatis untuk pengiriman baru<br>
                        • Pastikan semua field yang wajib (*) sudah diisi<br>
                        • Minimal harus ada 1 item barang
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Ubah data barang dari PHP menjadi variabel JavaScript yang bisa digunakan.
    const barangList = <?= json_encode($barang) ?>;

    /**
     * Fungsi untuk menambahkan baris item baru ke form.
     */
    function addItem() {
        const container = document.getElementById('itemsContainer');

        let optionsHtml = '';
        if (barangList && barangList.length > 0) {
            barangList.forEach(barang => {
                optionsHtml += `<option value="${barang.id_barang}">${escapeHtml(barang.nama)} (${escapeHtml(barang.satuan)})</option>`;
            });
        }

        // Template HTML untuk baris baru. Kita beri nama awal dengan indeks 0.
        // Fungsi reindexItems akan memperbaikinya nanti.
        const itemHtml = `
        <div class="item-row mb-3 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6>Item</h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-2">
                <select class="form-select form-select-sm" name="items[0][id_barang]" required>
                    <option value="">Pilih Barang</option>
                    ${optionsHtml}
                </select>
            </div>
            <div class="mb-2">
                <input type="number" class="form-control form-control-sm"
                       name="items[0][qty]" placeholder="Jumlah" min="1" required>
            </div>
            <div>
                <input type="text" class="form-control form-control-sm"
                       name="items[0][keterangan]" placeholder="Keterangan (opsional)">
            </div>
        </div>
        `;
        
        container.insertAdjacentHTML('beforeend', itemHtml);
        reindexItems();
    }

    /**
     * Fungsi untuk menghapus baris item dari form.
     */
    function removeItem(button) {
        const itemRows = document.querySelectorAll('.item-row');
        if (itemRows.length > 1) {
            button.closest('.item-row').remove();
            reindexItems();
        } else {
            alert('Minimal harus ada 1 item barang!');
        }
    }

    /**
     * Fungsi KUNCI: Memberi nomor ulang semua item di halaman.
     */
    function reindexItems() {
        const itemRows = document.querySelectorAll('.item-row');
        itemRows.forEach((row, index) => {
            // Update judul (Item 1, Item 2, dst.)
            const title = row.querySelector('h6');
            if (title) {
                title.textContent = `Item ${index + 1}`;
            }

            // Update atribut 'name' untuk semua input di dalam baris ini.
            const inputs = row.querySelectorAll('select, input');
            inputs.forEach(input => {
                if (input.name) {
                    // Regex ini akan mengganti items[APAPUN_DI_SINI] dengan indeks yang benar.
                    input.name = input.name.replace(/items\[.*?\]/, `items[${index}]`);
                }
            });
        });
    }

    // Fungsi kecil untuk keamanan (HTML escaping).
    function escapeHtml(text) {
        if (text === null || typeof text === 'undefined') return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, (m) => map[m]);
    }

    // Event listener untuk validasi form sebelum submit.
    document.getElementById('pengirimanForm').addEventListener('submit', function(e) {
        const itemRows = document.querySelectorAll('.item-row');
        if (itemRows.length === 0) {
            e.preventDefault();
            alert('Minimal harus ada 1 item barang!');
            return;
        }
        let isValid = true;
        let invalidItems = [];
        itemRows.forEach((row, index) => {
            const barangSelect = row.querySelector('select[name*="[id_barang]"]');
            const qtyInput = row.querySelector('input[name*="[qty]"]');
            if (!barangSelect || !barangSelect.value || !qtyInput || !qtyInput.value || parseInt(qtyInput.value) <= 0) {
                isValid = false;
                invalidItems.push(index + 1);
            }
        });
        if (!isValid) {
            e.preventDefault();
            alert(`Item berikut belum lengkap: ${invalidItems.join(', ')}\nPastikan semua item barang sudah dipilih dan jumlahnya sudah diisi dengan benar!`);
        }
    });

    // Event listener untuk auto-generate PO number.
    document.addEventListener('DOMContentLoaded', function() {
        const noPOField = document.getElementById('no_po');
        if (noPOField && !noPOField.value && !<?= isset($pengiriman) ? 'true' : 'false' ?>) {
            fetch('<?= base_url('pengiriman/generatePO') ?>', {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    noPOField.value = data.po_number;
                }
            })
            .catch(error => console.error('Error generating PO number:', error));
        }
    });
</script>
<?= $this->endSection() ?>