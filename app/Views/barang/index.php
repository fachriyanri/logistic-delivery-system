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
                    <a href="<?= base_url('barang/manage') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </a>
                </div>
                
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="get" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control" 
                                           placeholder="Cari barang..." 
                                           value="<?= esc($filter['keyword']) ?>">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="kategori" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($categories as $id => $nama): ?>
                                    <option value="<?= esc($id) ?>" <?= $filter['id_kategori'] === $id ? 'selected' : '' ?>>
                                        <?= esc($nama) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                            <?php if (!empty($filter['keyword']) || !empty($filter['id_kategori'])): ?>
                            <div class="col-md-2">
                                <a href="<?= base_url('barang') ?>" class="btn btn-secondary">
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
                                        <a href="<?= current_url() ?>?<?= http_build_query(array_merge($_GET, ['orderBy' => 'id_barang', 'orderType' => $orderType === 'ASC' ? 'DESC' : 'ASC'])) ?>" 
                                           class="text-white text-decoration-none">
                                            ID Barang
                                            <?php if ($orderBy === 'id_barang'): ?>
                                                <i class="fas fa-sort-<?= $orderType === 'ASC' ? 'up' : 'down' ?>"></i>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="<?= current_url() ?>?<?= http_build_query(array_merge($_GET, ['orderBy' => 'nama', 'orderType' => $orderType === 'ASC' ? 'DESC' : 'ASC'])) ?>" 
                                           class="text-white text-decoration-none">
                                            Nama Barang
                                            <?php if ($orderBy === 'nama'): ?>
                                                <i class="fas fa-sort-<?= $orderType === 'ASC' ? 'up' : 'down' ?>"></i>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th>Kategori</th>
                                    <th>Satuan</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $barang): ?>
                                    <tr>
                                        <td><?= esc($barang->id_barang) ?></td>
                                        <td><?= esc($barang->nama) ?></td>
                                        <td><?= esc($barang->getCategoryName()) ?></td>
                                        <td><?= esc($barang->satuan) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('barang/manage/' . $barang->id_barang) ?>" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('barang/delete/' . $barang->id_barang) ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')" 
                                                   title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data barang</td>
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