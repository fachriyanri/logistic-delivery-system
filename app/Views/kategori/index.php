<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
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

    // Set page actions for breadcrumb
    $pageActions = [
        [
            'title' => 'Add Category',
            'url' => base_url('kategori/manage'),
            'icon' => 'fas fa-plus',
            'class' => 'btn-primary'
        ]
    ];
    ?>

    <!-- Page Content -->
    <div class="row">
        <div class="col-12">
            <?= component('data_table', [
                'id' => 'categoriesTable',
                'columns' => $tableColumns,
                'data' => $tableData,
                'actions' => $tableActions,
                'searchable' => true,
                'sortable' => true,
                'paginated' => true,
                'perPage' => 15,
                'emptyMessage' => 'No categories found. Click "Add Category" to create your first category.',
                'class' => 'table-striped'
            ]) ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>