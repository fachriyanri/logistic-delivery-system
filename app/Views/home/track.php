<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link REL="SHORTCUT ICON" HREF="https://yusenlogistics-id.com//assets/global/images/logo/logo.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --bg-tertiary: #3a3a3a;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --accent-color: #007bff;
            --border-color: #404040;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: var(--bg-secondary) !important;
            border-bottom: 1px solid var(--border-color);
        }

        .navbar-brand img {
            filter: brightness(0) invert(1);
        }

        .navbar-nav .nav-link {
            color: var(--text-primary) !important;
        }

        .navbar-nav .nav-link:hover {
            color: var(--accent-color) !important;
        }

        .card {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .card-header {
            background-color: var(--bg-tertiary);
            border-bottom: 1px solid var(--border-color);
        }

        .form-control,
        .form-select {
            background-color: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .form-control:focus,
        .form-select:focus {
            background-color: var(--bg-tertiary);
            border-color: var(--accent-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .table-dark {
            --bs-table-bg: var(--bg-secondary);
            --bs-table-striped-bg: var(--bg-tertiary);
        }

        .badge {
            font-size: 0.75em;
        }

        .status-pending {
            background-color: var(--warning-color);
        }

        .status-progress {
            background-color: var(--accent-color);
        }

        .status-delivered {
            background-color: var(--success-color);
        }

        .status-cancelled {
            background-color: var(--danger-color);
        }

        .alert-info {
            background-color: rgba(0, 123, 255, 0.1);
            border-color: var(--accent-color);
            color: var(--text-primary);
        }

        .text-muted {
            color: var(--text-secondary) !important;
        }

        .border {
            border-color: var(--border-color) !important;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
            padding: 4rem 0;
            margin-bottom: 2rem;
        }

        .search-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .btn-outline-light:hover {
            background-color: var(--text-primary);
            color: var(--bg-primary);
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <img src="https://yusenlogistics-id.com/assets/global/images/logo/logo-white.png" alt="Puninar Yusen Logistics" height="40">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('/track') ?>">Track</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="text-center mb-4">
                <h1 class="display-4 fw-bold">Track Your Shipment</h1>
                <p class="lead">Enter your shipment ID, PO Number, or Recipient name to track your package</p>
            </div>

            <!-- Search Form -->
            <div class="search-container">
                <form method="GET" action="<?= current_url() ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-lg" name="search"
                                value="<?= esc($search ?? '') ?>"
                                placeholder="Enter ID, PO Number, or Recipient name"
                                required>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-lg" name="status">
                                <option value="">All Status</option>
                                <option value="1" <?= ($status ?? '') == '1' ? 'selected' : '' ?>>Pending</option>
                                <option value="2" <?= ($status ?? '') == '2' ? 'selected' : '' ?>>In Transit</option>
                                <option value="3" <?= ($status ?? '') == '3' ? 'selected' : '' ?>>Delivered</option>
                                <option value="4" <?= ($status ?? '') == '4' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-search"></i> Track
                            </button>
                        </div>
                    </div>

                    <!-- Date Filters -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">From Date</label>
                            <input type="date" class="form-control" name="tanggal_dari"
                                value="<?= esc($tanggal_dari ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">To Date</label>
                            <input type="date" class="form-control" name="tanggal_sampai"
                                value="<?= esc($tanggal_sampai ?? '') ?>">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="container mb-5">
        <?php if (!empty($search)): ?>
            <?php if (empty($pengiriman)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i>
                    No shipments found for your search criteria. Please check your search terms and try again.
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> Search Results
                            <span class="badge bg-primary"><?= $total ?> found</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-striped mb-0">
                                <thead>
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
                                                        $statusClass = 'status-pending';
                                                        $statusText = 'Pending';
                                                        break;
                                                    case 2:
                                                        $statusClass = 'status-progress';
                                                        $statusText = 'Dalam Perjalanan';
                                                        break;
                                                    case 3:
                                                        $statusClass = 'status-delivered';
                                                        $statusText = 'Terkirim';
                                                        break;
                                                    case 4:
                                                        $statusClass = 'status-cancelled';
                                                        $statusText = 'Dibatalkan';
                                                        break;
                                                    default:
                                                        $statusClass = 'bg-secondary';
                                                        $statusText = 'Unknown';
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('/track/detail/' . $item->id_pengiriman) ?>"
                                                    class="btn btn-sm btn-outline-light">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if (isset($pager) && $total > 0): ?>
                        <div class="d-flex justify-content-between align-items-center mt-4 mb-4 px-3 py-3">
                            <div>
                                <small class="text-muted">
                                    Showing <?= ($currentPage - 1) * $perPage + 1 ?> - 
                                    <?= min($currentPage * $perPage, $total) ?> of <?= $total ?> shipments
                                </small>
                            </div>
                            <div>
                                <?= $pager->links() ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center">
                <div class="card">
                    <div class="card-body py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>Ready to Track Your Shipment?</h4>
                        <p class="text-muted">Enter your search criteria above to find your shipment information.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0 text-muted">
                &copy; <?= date('Y') ?> PT Puninar Yusen Logistics Indonesia. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>