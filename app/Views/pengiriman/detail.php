<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Detail Pengiriman - <?= esc($pengiriman->id_pengiriman) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Detail Pengiriman</h1>
            <p class="mb-0 text-muted">ID: <?= esc($pengiriman->id_pengiriman) ?></p>
        </div>
        <div>
            <a href="<?= base_url('pengiriman') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <?php if (session('level') == 1 || session('level') == 2 || session('level') == 3): ?>
            <a href="<?= base_url('pengiriman/edit/' . $pengiriman->id_pengiriman) ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <?php endif; ?>
            <a href="<?= base_url('pengiriman/delivery-note/' . $pengiriman->id_pengiriman) ?>" 
               class="btn btn-success" target="_blank">
                <i class="fas fa-file-pdf"></i> Surat Jalan
            </a>
            <a href="<?= base_url('pengiriman/qr/' . $pengiriman->id_pengiriman) ?>" 
               class="btn btn-primary" target="_blank">
                <i class="fas fa-qrcode"></i> QR Code
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= $this->include('layouts/partials/flash_messages') ?>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <!-- Basic Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pengiriman</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>ID Pengiriman</strong></td>
                                    <td>: <?= esc($pengiriman->id_pengiriman) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal</strong></td>
                                    <td>: <?= date('d F Y', strtotime($pengiriman->tanggal)) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>No PO</strong></td>
                                    <td>: <?= esc($pengiriman->no_po) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>No Kendaraan</strong></td>
                                    <td>: <?= esc($pengiriman->no_kendaraan) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Penerima</strong></td>
                                    <td>: <?= esc($pengiriman->penerima) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Status</strong></td>
                                    <td>: 
                                        <?php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch ($pengiriman->status) {
                                            case 1:
                                                $statusClass = 'warning';
                                                $statusText = 'Pending';
                                                break;
                                            case 2:
                                                $statusClass = 'info';
                                                $statusText = 'Dalam Perjalanan';
                                                break;
                                            case 3:
                                                $statusClass = 'success';
                                                $statusText = 'Terkirim';
                                                break;
                                            case 4:
                                                $statusClass = 'danger';
                                                $statusText = 'Dibatalkan';
                                                break;
                                        }
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?> fs-6"><?= $statusText ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat</strong></td>
                                    <td>: <?= isset($pengiriman->created_at) ? date('d/m/Y H:i', strtotime($pengiriman->created_at)) : '-' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Diupdate</strong></td>
                                    <td>: <?= isset($pengiriman->updated_at) ? date('d/m/Y H:i', strtotime($pengiriman->updated_at)) : '-' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Keterangan</strong></td>
                                    <td>: <?= !empty($pengiriman->keterangan) ? esc($pengiriman->keterangan) : '-' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary"><?= esc($pengiriman->nama_pelanggan) ?></h6>
                            <p class="mb-1">
                                <i class="fas fa-map-marker-alt text-muted"></i> 
                                <?= esc($pengiriman->alamat_pelanggan) ?>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-phone text-muted"></i> 
                                <?= esc($pengiriman->telepon_pelanggan) ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-info">Kurir: <?= esc($pengiriman->nama_kurir) ?></h6>
                            <p class="mb-1">
                                <i class="fas fa-map-marker-alt text-muted"></i> 
                                <?= esc($pengiriman->alamat_kurir) ?>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-phone text-muted"></i> 
                                <?= esc($pengiriman->telepon_kurir) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Barang</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th width="10%">Jumlah</th>
                                    <th width="10%">Satuan</th>
                                    <th width="15%">Harga Satuan</th>
                                    <th width="15%">Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($detail_pengiriman)): ?>
                                    <?php 
                                    $totalNilai = 0;
                                    foreach ($detail_pengiriman as $index => $detail): 
                                        $subtotal = $detail->qty  * $detail->harga;
                                        $totalNilai += $subtotal;
                                    ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <strong><?= esc($detail->nama_barang) ?></strong>
                                        </td>
                                        <td><?= esc($detail->nama_kategori) ?></td>
                                        <td class="text-center"><?= number_format($detail->qty) ?></td>
                                        <td class="text-center"><?= esc($detail->satuan) ?></td>
                                        <td class="text-end">Rp <?= number_format($detail->harga, 0, ',', '.') ?></td>
                                        <td class="text-end">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                        <td><?= !empty($detail->keterangan) ? esc($detail->keterangan) : '-' ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-info">
                                        <td colspan="6" class="text-end"><strong>Total Nilai:</strong></td>
                                        <td class="text-end"><strong>Rp <?= number_format($totalNilai, 0, ',', '.') ?></strong></td>
                                        <td></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                                <p>Tidak ada detail barang</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Update Card -->
            <?php if (session('level') == 1 || session('level') == 3): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Status</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('pengiriman/update-status/' . $pengiriman->id_pengiriman) ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status Baru</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="1" <?= $pengiriman->status == 1 ? 'selected' : '' ?>>Pending</option>
                                <option value="2" <?= $pengiriman->status == 2 ? 'selected' : '' ?>>Dalam Perjalanan</option>
                                <option value="3" <?= $pengiriman->status == 3 ? 'selected' : '' ?>>Terkirim</option>
                                <option value="4" <?= $pengiriman->status == 4 ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-sync"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Photo Card -->
            <?php if (!empty($pengiriman->photo)): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Foto Bukti Pengiriman</h6>
                </div>
                <div class="card-body text-center">
                    <img src="<?= base_url('uploads/pengiriman/' . $pengiriman->photo) ?>" 
                         alt="Foto Pengiriman" class="img-fluid rounded" 
                         style="max-height: 300px; cursor: pointer;"
                         onclick="showImageModal(this.src)">
                    <div class="mt-2">
                        <small class="text-muted">Klik untuk memperbesar</small>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- QR Code Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">QR Code Tracking</h6>
                </div>
                <div class="card-body text-center">
                    <div id="qrcode" class="mb-3"></div>
                    <p class="small text-muted">Scan QR Code untuk tracking pengiriman</p>
                    <a href="<?= base_url('pengiriman/qr/' . $pengiriman->id_pengiriman) ?>" 
                       class="btn btn-primary btn-sm" target="_blank">
                        <i class="fas fa-download"></i> Download QR
                    </a>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Lainnya</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('pengiriman/delivery-note/' . $pengiriman->id_pengiriman) ?>" 
                           class="btn btn-success btn-sm" target="_blank">
                            <i class="fas fa-file-pdf"></i> Cetak Surat Jalan
                        </a>
                        <a href="<?= base_url('pengiriman/track/' . $pengiriman->id_pengiriman) ?>" 
                           class="btn btn-info btn-sm">
                            <i class="fas fa-route"></i> Tracking Pengiriman
                        </a>
                        <?php if (session('level') == 1): ?>
                        <button type="button" class="btn btn-danger btn-sm" 
                                onclick="confirmDelete('<?= $pengiriman->id_pengiriman ?>')">
                            <i class="fas fa-trash"></i> Hapus Pengiriman
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Bukti Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Foto Pengiriman" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<?php if (session('level') == 1): ?>
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengiriman ini?</p>
                <p class="text-danger"><strong>Perhatian:</strong> Data yang dihapus tidak dapat dikembalikan!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Include QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>

<script>
// Generate QR Code
document.addEventListener('DOMContentLoaded', function() {
    const trackingUrl = '<?= base_url('track/' . $pengiriman->id_pengiriman) ?>';
    
    QRCode.toCanvas(document.getElementById('qrcode'), trackingUrl, {
        width: 150,
        height: 150,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M
    }, function (error) {
        if (error) console.error(error);
    });
});

// Show image modal
function showImageModal(src) {
    document.getElementById('modalImage').src = src;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

// Confirm delete
<?php if (session('level') == 1): ?>
function confirmDelete(id) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = '<?= base_url('pengiriman/delete') ?>/' + id;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
<?php endif; ?>

// Auto-hide flash messages
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
<?= $this->endSection() ?>