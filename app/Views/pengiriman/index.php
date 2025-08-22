<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Data Pengiriman<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Data Pengiriman</h1>
            <p class="mb-0 text-muted">Kelola data pengiriman barang</p>
        </div>
        <?php if (session('level') == 1 || session('level') == 3): ?>
        <a href="<?= base_url('pengiriman/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Pengiriman
        </a>
        <?php endif; ?>
    </div>

    <!-- Flash Messages -->
    <?= $this->include('layouts/partials/flash_messages') ?>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= current_url() ?>" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Cari</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= esc($search ?? '') ?>" placeholder="ID/No PO/Penerima">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="1" <?= ($status ?? '') == '1' ? 'selected' : '' ?>>Pending</option>
                        <option value="2" <?= ($status ?? '') == '2' ? 'selected' : '' ?>>Dalam Perjalanan</option>
                        <option value="3" <?= ($status ?? '') == '3' ? 'selected' : '' ?>>Terkirim</option>
                        <option value="4" <?= ($status ?? '') == '4' ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tanggal_dari" class="form-label">Tanggal Dari</label>
                    <input type="date" class="form-control" id="tanggal_dari" name="tanggal_dari" 
                           value="<?= esc($tanggal_dari ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="tanggal_sampai" class="form-label">Tanggal Sampai</label>
                    <input type="date" class="form-control" id="tanggal_sampai" name="tanggal_sampai" 
                           value="<?= esc($tanggal_sampai ?? '') ?>">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="<?= current_url() ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                    <a href="<?= base_url('pengiriman/export') ?>?<?= http_build_query($_GET) ?>" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengiriman</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>ID Pengiriman</th>
                            <th>Tanggal</th>
                            <th>No PO</th>
                            <th>Pelanggan</th>
                            <th>Kurir</th>
                            <th>No Kendaraan</th>
                            <th>Penerima</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pengiriman)): ?>
                            <?php foreach ($pengiriman as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 + (($currentPage - 1) * $perPage) ?></td>
                                <td>
                                    <strong><?= esc($item->id_pengiriman) ?></strong>
                                    <?php if (!empty($item->photo)): ?>
                                        <br><small class="text-success"><i class="fas fa-camera"></i> Ada Foto</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($item->tanggal)) ?></td>
                                <td><?= esc($item->no_po) ?></td>
                                <td>
                                    <strong><?= esc($item->nama_pelanggan) ?></strong>
                                    <br><small class="text-muted"><?= esc($item->alamat_pelanggan) ?></small>
                                </td>
                                <td>
                                    <?= esc($item->nama_kurir) ?>
                                    <br><small class="text-muted"><?= esc($item->telepon_kurir) ?></small>
                                </td>
                                <td><?= esc($item->no_kendaraan) ?></td>
                                <td><?= esc($item->penerima) ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($item->status) {
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
                                        default:
                                            $statusClass = 'secondary';
                                            $statusText = 'Unknown';
                                    }
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('pengiriman/detail/' . $item->id_pengiriman) ?>" 
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if (session('level') == 1 || session('level') == 3): ?>
                                        <a href="<?= base_url('pengiriman/edit/' . $item->id_pengiriman) ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <a href="<?= base_url('pengiriman/delivery-note/' . $item->id_pengiriman) ?>" 
                                           class="btn btn-sm btn-success" title="Surat Jalan" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        
                                        <a href="<?= base_url('pengiriman/qr/' . $item->id_pengiriman) ?>" 
                                           class="btn btn-sm btn-primary" title="QR Code" target="_blank">
                                            <i class="fas fa-qrcode"></i>
                                        </a>
                                        
                                        <?php if (session('level') == 1): ?>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete('<?= $item->id_pengiriman ?>')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>Tidak ada data pengiriman</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($pager)): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Menampilkan <?= ($currentPage - 1) * $perPage + 1 ?> - 
                        <?= min($currentPage * $perPage, $total) ?> dari <?= $total ?> data
                    </small>
                </div>
                <div>
                    <?= $pager->links() ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data pengiriman ini?</p>
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

<script>
function confirmDelete(id) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = '<?= base_url('pengiriman/delete') ?>/' + id;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Initialize DataTable if needed
document.addEventListener('DOMContentLoaded', function() {
    // Add any additional JavaScript functionality here
    
    // Auto-hide flash messages after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
<?= $this->endSection() ?>