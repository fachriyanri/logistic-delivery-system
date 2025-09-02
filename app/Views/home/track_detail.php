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

        .table-dark {
            --bs-table-bg: var(--bg-secondary);
            --bs-table-striped-bg: var(--bg-tertiary);
        }

        .badge {
            font-size: 0.75em;
        }

        .status-pending { background-color: var(--warning-color); }
        .status-progress { background-color: var(--accent-color); }
        .status-delivered { background-color: var(--success-color); }
        .status-cancelled { background-color: var(--danger-color); }

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
                <i class="fas fa-arrow-left"></i> Back to Search
            </a>
        </div>

        <!-- Shipment Information -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shipping-fast"></i> Shipment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Shipment ID</div>
                                    <div class="info-value fw-bold"><?= esc($pengiriman->id_pengiriman) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">PO Number</div>
                                    <div class="info-value"><?= esc($pengiriman->no_po) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Shipment Date</div>
                                    <div class="info-value"><?= date('d F Y', strtotime($pengiriman->tanggal_pengiriman)) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Status</div>
                                    <div class="info-value">
                                        <?php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch($pengiriman->status) {
                                            case 1:
                                                $statusClass = 'status-pending';
                                                $statusText = 'Pending';
                                                break;
                                            case 2:
                                                $statusClass = 'status-progress';
                                                $statusText = 'In Transit';
                                                break;
                                            case 3:
                                                $statusClass = 'status-delivered';
                                                $statusText = 'Delivered';
                                                break;
                                            case 4:
                                                $statusClass = 'status-cancelled';
                                                $statusText = 'Cancelled';
                                                break;
                                            default:
                                                $statusClass = 'bg-secondary';
                                                $statusText = 'Unknown';
                                        }
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Courier</div>
                                    <div class="info-value"><?= esc($pengiriman->nama_kurir ?? 'Not assigned') ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Courier Phone</div>
                                    <div class="info-value"><?= esc($pengiriman->no_hp_kurir ?? '-') ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Total Weight</div>
                                    <div class="info-value"><?= number_format($pengiriman->total_berat, 2) ?> kg</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Shipping Cost</div>
                                    <div class="info-value">Rp <?= number_format($pengiriman->biaya_pengiriman, 0, ',', '.') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user"></i> Customer Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Sender</div>
                                    <div class="info-value"><?= esc($pengiriman->nama_pengirim ?? $pengiriman->nama_pelanggan) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Sender Phone</div>
                                    <div class="info-value"><?= esc($pengiriman->no_hp_pengirim ?? $pengiriman->no_hp_pelanggan) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Origin Address</div>
                                    <div class="info-value"><?= esc($pengiriman->alamat_asal) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Recipient</div>
                                    <div class="info-value"><?= esc($pengiriman->penerima) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Recipient Phone</div>
                                    <div class="info-value"><?= esc($pengiriman->no_hp_penerima) ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Destination Address</div>
                                    <div class="info-value"><?= esc($pengiriman->alamat_tujuan) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Details -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-boxes"></i> Item Details
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Category</th>
                                        <th>Quantity</th>
                                        <th>Weight (kg)</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($detail_pengiriman)): ?>
                                        <?php foreach ($detail_pengiriman as $detail): ?>
                                        <tr>
                                            <td><?= esc($detail->nama_barang) ?></td>
                                            <td>
                                                <span class="badge bg-info"><?= esc($detail->nama_kategori) ?></span>
                                            </td>
                                            <td><?= number_format($detail->jumlah) ?></td>
                                            <td><?= number_format($detail->berat, 2) ?></td>
                                            <td>
                                                <small class="text-muted"><?= esc($detail->keterangan ?: '-') ?></small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No item details available</td>
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
                                    <small class="text-muted">Package is on the way</small>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($pengiriman->status == 3): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Delivered</h6>
                                    <small class="text-muted">Package delivered successfully</small>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($pengiriman->status == 4): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Cancelled</h6>
                                    <small class="text-muted">Shipment was cancelled</small>
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