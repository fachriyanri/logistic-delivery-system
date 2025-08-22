<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
.stats-card {
    transition: transform 0.2s ease-in-out;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-900);
}

.stats-label {
    color: var(--gray-600);
    font-size: 0.875rem;
    font-weight: 500;
}

.quick-action-card {
    transition: all 0.2s ease-in-out;
    cursor: pointer;
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.recent-activity-item {
    padding: 1rem;
    border-bottom: 1px solid var(--gray-200);
    transition: background-color 0.2s ease-in-out;
}

.recent-activity-item:hover {
    background-color: var(--gray-50);
}

.recent-activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h2 class="mb-1">Welcome back, <?= esc(session('username')) ?>!</h2>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <?= date('l, F j, Y') ?>
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-primary fs-6 px-3 py-2">
                                <i class="fas fa-user-tag me-2"></i>
                                <?= ucfirst(session('role_name')) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <?= component('analytics_card', [
                'title' => 'Total Shipments',
                'value' => $stats['total_shipments'] ?? 0,
                'subtitle' => 'All time',
                'icon' => 'fas fa-shipping-fast',
                'color' => 'primary',
                'trend' => [
                    'value' => '+12%',
                    'direction' => 'up',
                    'period' => 'vs last month'
                ],
                'sparkline' => [45, 52, 48, 61, 55, 67, 73, 69, 78, 85, 89, 92],
                'url' => base_url('/pengiriman')
            ]) ?>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <?= component('analytics_card', [
                'title' => 'Delivered',
                'value' => $stats['delivered_shipments'] ?? 0,
                'subtitle' => 'Completed shipments',
                'icon' => 'fas fa-check-circle',
                'color' => 'success',
                'trend' => [
                    'value' => '+8%',
                    'direction' => 'up',
                    'period' => 'vs last month',
                    'progress' => 85
                ],
                'sparkline' => [30, 35, 42, 48, 52, 58, 65, 62, 68, 75, 78, 82],
                'url' => base_url('/pengiriman?status=delivered')
            ]) ?>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <?= component('analytics_card', [
                'title' => 'Pending',
                'value' => $stats['pending_shipments'] ?? 0,
                'subtitle' => 'Awaiting delivery',
                'icon' => 'fas fa-clock',
                'color' => 'warning',
                'trend' => [
                    'value' => '-5%',
                    'direction' => 'down',
                    'period' => 'vs last month'
                ],
                'sparkline' => [25, 28, 22, 18, 15, 12, 18, 22, 19, 16, 14, 11],
                'url' => base_url('/pengiriman?status=pending')
            ]) ?>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <?= component('analytics_card', [
                'title' => 'Customers',
                'value' => $stats['total_customers'] ?? 0,
                'subtitle' => 'Active customers',
                'icon' => 'fas fa-users',
                'color' => 'info',
                'trend' => [
                    'value' => '+3%',
                    'direction' => 'up',
                    'period' => 'vs last month'
                ],
                'sparkline' => [120, 125, 128, 132, 135, 138, 142, 145, 148, 152, 155, 158],
                'url' => base_url('/pelanggan')
            ]) ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (session('level') == 1 || session('level') == 3): ?>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card quick-action-card h-100" onclick="window.location.href='<?= base_url('/pengiriman/create') ?>'">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-plus-circle fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="card-title">New Shipment</h6>
                                    <p class="card-text text-muted small">Create a new shipment record</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (session('level') == 1 || session('level') == 2): ?>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card quick-action-card h-100" onclick="window.location.href='<?= base_url('/pelanggan/create') ?>'">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-user-plus fa-2x text-success"></i>
                                    </div>
                                    <h6 class="card-title">Add Customer</h6>
                                    <p class="card-text text-muted small">Register a new customer</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (session('level') == 1 || session('level') == 2): ?>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card quick-action-card h-100" onclick="window.location.href='<?= base_url('/laporan') ?>'">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-chart-line fa-2x text-info"></i>
                                    </div>
                                    <h6 class="card-title">View Reports</h6>
                                    <p class="card-text text-muted small">Generate shipping reports</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (session('level') == 1 || session('level') == 3): ?>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card quick-action-card h-100" onclick="window.location.href='<?= base_url('/barang/create') ?>'">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-box fa-2x text-warning"></i>
                                    </div>
                                    <h6 class="card-title">Add Item</h6>
                                    <p class="card-text text-muted small">Add new inventory item</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Shipment Trends
                    </h5>
                </div>
                <div class="card-body">
                    <?= component('chart', [
                        'id' => 'shipmentTrendsChart',
                        'type' => 'line',
                        'height' => '350px',
                        'data' => [
                            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                            'datasets' => [
                                [
                                    'label' => 'Total Shipments',
                                    'data' => [45, 52, 48, 61, 55, 67, 73, 69, 78, 85, 89, 92],
                                    'borderColor' => 'rgb(26, 35, 50)',
                                    'backgroundColor' => 'rgba(26, 35, 50, 0.1)',
                                    'tension' => 0.4,
                                    'fill' => true
                                ],
                                [
                                    'label' => 'Delivered',
                                    'data' => [30, 35, 42, 48, 52, 58, 65, 62, 68, 75, 78, 82],
                                    'borderColor' => 'rgb(40, 167, 69)',
                                    'backgroundColor' => 'rgba(40, 167, 69, 0.1)',
                                    'tension' => 0.4,
                                    'fill' => true
                                ]
                            ]
                        ],
                        'options' => [
                            'scales' => [
                                'y' => [
                                    'beginAtZero' => true,
                                    'grid' => [
                                        'color' => 'rgba(0, 0, 0, 0.1)'
                                    ]
                                ],
                                'x' => [
                                    'grid' => [
                                        'display' => false
                                    ]
                                ]
                            ],
                            'plugins' => [
                                'legend' => [
                                    'position' => 'top'
                                ]
                            ]
                        ]
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>
                        Shipment Status
                    </h5>
                </div>
                <div class="card-body">
                    <?= component('chart', [
                        'id' => 'shipmentStatusChart',
                        'type' => 'doughnut',
                        'height' => '350px',
                        'data' => [
                            'labels' => ['Delivered', 'In Transit', 'Pending', 'Cancelled'],
                            'datasets' => [
                                [
                                    'data' => [
                                        $stats['delivered_shipments'] ?? 0,
                                        ($stats['total_shipments'] ?? 0) - ($stats['delivered_shipments'] ?? 0) - ($stats['pending_shipments'] ?? 0),
                                        $stats['pending_shipments'] ?? 0,
                                        5 // Sample cancelled shipments
                                    ],
                                    'backgroundColor' => [
                                        'rgb(40, 167, 69)',   // Success - Delivered
                                        'rgb(23, 162, 184)',  // Info - In Transit
                                        'rgb(255, 193, 7)',   // Warning - Pending
                                        'rgb(220, 53, 69)'    // Danger - Cancelled
                                    ],
                                    'borderWidth' => 2,
                                    'borderColor' => '#fff'
                                ]
                            ]
                        ],
                        'options' => [
                            'plugins' => [
                                'legend' => [
                                    'position' => 'bottom'
                                ]
                            ],
                            'cutout' => '60%'
                        ]
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and System Status -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-history me-2"></i>
                        Recent Activity
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recent_activities)): ?>
                        <?php foreach ($recent_activities as $activity): ?>
                        <div class="recent-activity-item">
                            <div class="d-flex align-items-center">
                                <div class="activity-icon bg-<?= $activity['type'] ?> bg-opacity-10 text-<?= $activity['type'] ?> me-3">
                                    <i class="<?= $activity['icon'] ?>"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-medium"><?= esc($activity['title']) ?></div>
                                    <div class="text-muted small"><?= esc($activity['description']) ?></div>
                                </div>
                                <div class="text-muted small">
                                    <?= $activity['time'] ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No recent activity</h6>
                            <p class="text-muted small">Activity will appear here as you use the system</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>
                        System Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Database</div>
                            <div class="text-muted small">Connection status</div>
                        </div>
                        <div class="badge bg-success">
                            <i class="fas fa-check me-1"></i>
                            Online
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Storage</div>
                            <div class="text-muted small">File system status</div>
                        </div>
                        <div class="badge bg-success">
                            <i class="fas fa-check me-1"></i>
                            Available
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="fw-medium">Last Backup</div>
                            <div class="text-muted small">System backup status</div>
                        </div>
                        <div class="badge bg-info">
                            <i class="fas fa-clock me-1"></i>
                            <?= date('M j, Y') ?>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-server me-1"></i>
                            System Version: <?= APP_VERSION ?? '1.0.0' ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to stats cards
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add click handlers for quick action cards
    const quickActionCards = document.querySelectorAll('.quick-action-card');
    quickActionCards.forEach(card => {
        card.addEventListener('click', function() {
            const url = this.getAttribute('onclick');
            if (url) {
                // Extract URL from onclick attribute
                const match = url.match(/window\.location\.href='([^']+)'/);
                if (match) {
                    window.location.href = match[1];
                }
            }
        });
    });

    // Simulate real-time updates (for demo purposes)
    setInterval(function() {
        const timestamp = document.querySelector('.recent-activity-item .text-muted.small');
        if (timestamp) {
            // Update relative time display
            console.log('Updating timestamps...');
        }
    }, 60000); // Update every minute
});
</script>
<?= $this->endSection() ?>