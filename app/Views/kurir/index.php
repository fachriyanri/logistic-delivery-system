<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><?= $title ?></h3>
                    <a href="<?= base_url('kurir/manage') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Kurir
                    </a>
                </div>
                
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="get" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control" 
                                           placeholder="Cari kurir..." 
                                           value="<?= esc($filter['keyword']) ?>">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="gender" class="form-select">
                                    <option value="">Semua Jenis Kelamin</option>
                                    <?php foreach ($genderOptions as $value => $label): ?>
                                    <option value="<?= esc($value) ?>" <?= $filter['jenis_kelamin'] === $value ? 'selected' : '' ?>>
                                        <?= esc($label) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                            <?php if (!empty($filter['keyword']) || !empty($filter['jenis_kelamin'])): ?>
                            <div class="col-md-2">
                                <a href="<?= base_url('kurir') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </form>

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

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>
                                        <a href="<?= current_url() ?>?<?= http_build_query(array_merge($_GET, ['orderBy' => 'id_kurir', 'orderType' => $orderType === 'ASC' ? 'DESC' : 'ASC'])) ?>" 
                                           class="text-white text-decoration-none">
                                            ID Kurir
                                            <?php if ($orderBy === 'id_kurir'): ?>
                                                <i class="fas fa-sort-<?= $orderType === 'ASC' ? 'up' : 'down' ?>"></i>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="<?= current_url() ?>?<?= http_build_query(array_merge($_GET, ['orderBy' => 'nama', 'orderType' => $orderType === 'ASC' ? 'DESC' : 'ASC'])) ?>" 
                                           class="text-white text-decoration-none">
                                            Nama Kurir
                                            <?php if ($orderBy === 'nama'): ?>
                                                <i class="fas fa-sort-<?= $orderType === 'ASC' ? 'up' : 'down' ?>"></i>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th>Jenis Kelamin</th>
                                    <th>Telepon</th>
                                    <th>Alamat</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($couriers)): ?>
                                    <?php foreach ($couriers as $kurir): ?>
                                    <tr>
                                        <td><?= esc($kurir->id_kurir) ?></td>
                                        <td><?= esc($kurir->nama) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $kurir->isMale() ? 'primary' : 'info' ?>">
                                                <?= esc($kurir->jenis_kelamin) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($kurir->getFormattedPhone()) ?></td>
                                        <td><?= esc($kurir->alamat ?: '-') ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('kurir/manage/' . $kurir->id_kurir) ?>" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('kurir/delete/' . $kurir->id_kurir) ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus kurir ini?')" 
                                                   title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data kurir</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pager->getPageCount() > 1): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Menampilkan <?= ($currentPage - 1) * 15 + 1 ?> - <?= min($currentPage * 15, $total) ?> dari <?= $total ?> data
                        </div>
                        <div>
                            <?= $pager->links() ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>