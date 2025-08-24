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
                    <a href="<?= base_url('kategori/manage') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Kategori
                    </a>
                </div>
                
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="get" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control" 
                                           placeholder="Cari kategori..." 
                                           value="<?= esc($filter['keyword']) ?>">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <?php if (!empty($filter['keyword'])): ?>
                            <div class="col-md-2">
                                <a href="<?= base_url('kategori') ?>" class="btn btn-secondary">
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
                    <?php
                    // Prepare data for the data table component
                    $tableColumns = [
                        'id_kategori' => [
                            'title' => 'ID Kategori',
                            'sortable' => true,
                            'width' => '120px'
                        ],
                        'nama' => [
                            'title' => 'Nama Kategori',
                            'sortable' => true
                        ],
                        'keterangan' => [
                            'title' => 'Keterangan',
                            'sortable' => true,
                            'render' => function($value, $row) {
                                return $value ?: '<span class="text-muted">-</span>';
                            }
                        ]
                    ];

                    $tableData = [];
                    if (!empty($categories)) {
                        foreach ($categories as $kategori) {
                            $tableData[] = [
                                'id_kategori' => $kategori->id_kategori,
                                'nama' => $kategori->nama,
                                'keterangan' => $kategori->keterangan
                            ];
                        }
                    }

                    $tableActions = [
                        [
                            'title' => 'Edit',
                            'url' => base_url('kategori/manage/{id_kategori}'),
                            'icon' => 'fas fa-edit',
                            'class' => 'btn-outline-warning'
                        ],
                        [
                            'title' => 'Delete',
                            'url' => base_url('kategori/delete/{id_kategori}'),
                            'icon' => 'fas fa-trash',
                            'class' => 'btn-outline-danger',
                            'confirm' => 'Are you sure you want to delete this category?'
                        ]
                    ];
                    ?>

                    <div class="table-responsive">
                        <?= component('data_table', [
                            'id' => 'categoriesTable',
                            'columns' => $tableColumns,
                            'data' => $tableData,
                            'actions' => $tableActions,
                            'searchable' => false, // We're handling search in the form above
                            'sortable' => true,
                            'paginated' => true,
                            'perPage' => 15,
                            'emptyMessage' => 'No categories found. Click "Add Category" to create your first category.',
                            'class' => 'table-striped'
                        ]) ?>
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