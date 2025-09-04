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

        .table-bordered {
            --bs-table-bg: var(--bg-secondary);
            --bs-table-striped-bg: var(--bg-tertiary);
            border-color: var(--border-color);
        }

        .table-bordered th,
        .table-bordered td {
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .table-light {
            --bs-table-bg: var(--bg-tertiary);
            --bs-table-color: var(--text-primary);
        }

        .table-info {
            --bs-table-bg: rgba(13, 202, 240, 0.2);
            --bs-table-color: var(--text-primary);
        }

        /* White text styling for Detail Barang header only */
        .detail-barang-header {
            color: #ffffff !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
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

        .text-muted {
            color: var(--text-secondary) !important;
        }

        .border {
            border-color: var(--border-color) !important;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-outline-light:hover {
            background-color: var(--text-primary);
            color: var(--bg-primary);
        }

        .info-row {
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-secondary);
        }

        .info-value {
            color: var(--text-primary);
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
                        <a class="nav-link" href="<?= base_url('/track') ?>">Track</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="<?= base_url('/track') ?>" class="btn btn-outline-light">
                <i class="fas fa-arrow-left"></i> Kembali ke pencarian
            </a>
        </div>

        <!-- Shipment Information -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shipping-fast"></i> Informasi Pengiriman
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">ID Pengiriman</div>
                                    <div class="info-value fw-bold"><?= esc($pengiriman->id_pengiriman) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Tanggal</div>
                                    <div class="info-value"><?= date('d F Y', strtotime($pengiriman->tanggal)) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">No PO</div>
                                    <div class="info-value"><?= esc($pengiriman->no_po) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">No Kendaraan</div>
                                    <div class="info-value"><?= esc($pengiriman->no_kendaraan) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Penerima</div>
                                    <div class="info-value"><?= !empty($pengiriman->penerima) ? esc($pengiriman->penerima) : '-' ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Status</div>
                                    <div class="info-value">
                                        <?php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch ($pengiriman->status) {
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
                                    </div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Dibuat</div>
                                    <div class="info-value"><?= isset($pengiriman->created_at) ? date('d/m/Y H:i', strtotime($pengiriman->created_at)) : '-' ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Diupdate</div>
                                    <div class="info-value"><?= isset($pengiriman->updated_at) ? date('d/m/Y H:i', strtotime($pengiriman->updated_at)) : '-' ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Keterangan</div>
                                    <div class="info-value"> <?= !empty($pengiriman->keterangan) ? esc($pengiriman->keterangan) : '-' ?> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user"></i> Informasi Pelanggan
                        </h5>
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

                <!-- Item Details -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-user"></i> Detail Barang
                        </h5>
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

            <!-- Tracking Actions -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-route"></i> Tracking Actions
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <?php if (!empty($pengiriman->id_pengiriman)): ?>
                            <a href="<?= base_url('pengiriman/track/' . $pengiriman->id_pengiriman) ?>"
                                class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-map-marker-alt"></i> Track Shipment
                            </a>
                        <?php endif; ?>

                        <div class="text-muted">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                For more detailed tracking information, use the Track Shipment button above.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-clock"></i> Timeline
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Order Created</h6>
                                    <small class="text-muted">
                                        <?= date('d M Y, H:i', strtotime($pengiriman->created_at ?? $pengiriman->tanggal_pengiriman)) ?>
                                    </small>
                                </div>
                            </div>

                            <?php if ($pengiriman->status >= 2): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">In Transit</h6>
                                        <small class="text-muted">
                                            <?= isset($pengiriman->updated_at) ? date('d M Y, H:i', strtotime($pengiriman->updated_at)) : 'Package is on the way' ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($pengiriman->status == 3): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Delivered</h6>
                                        <small class="text-muted">
                                            <?= isset($pengiriman->updated_at) ? date('d M Y, H:i', strtotime($pengiriman->updated_at)) : 'Package delivered successfully' ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($pengiriman->status == 4): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-danger"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Cancelled</h6>
                                        <small class="text-muted">
                                            <?= isset($pengiriman->updated_at) ? date('d M Y, H:i', strtotime($pengiriman->updated_at)) : 'Shipment was cancelled' ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border-color);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -22px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid var(--bg-secondary);
        }

        .timeline-content h6 {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
    </style>
</body>

</html>