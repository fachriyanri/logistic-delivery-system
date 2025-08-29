<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<div class="mobile-tracking-container">
    <!-- Header -->
    <div class="tracking-header">
        <div class="d-flex align-items-center mb-3">
            <button class="btn btn-outline-secondary btn-sm me-3" onclick="history.back()">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div>
                <h4 class="mb-0">Shipment Tracking</h4>
                <small class="text-muted">ID: <?= esc($shipment['shipment_id']) ?></small>
            </div>
        </div>
    </div>

    <!-- Status Card -->
    <div class="status-card mb-4">
        <div class="status-icon">
            <?php
            $statusIcons = [
                'Pending' => 'fas fa-clock text-warning',
                'In Transit' => 'fas fa-truck text-info',
                'Delivered' => 'fas fa-check-circle text-success',
                'Cancelled' => 'fas fa-times-circle text-danger'
            ];
            $iconClass = $statusIcons[$shipment['status']] ?? 'fas fa-question-circle text-muted';
            ?>
            <i class="<?= $iconClass ?> fa-2x"></i>
        </div>
        <div class="status-info">
            <h5 class="status-text"><?= esc($shipment['status']) ?></h5>
            <p class="status-date mb-0">
                <i class="fas fa-calendar me-2"></i>
                <?= date('M d, Y', strtotime($shipment['date'])) ?>
            </p>
        </div>
    </div>

    <!-- Shipment Details -->
    <div class="details-section">
        <h6 class="section-title">
            <i class="fas fa-info-circle me-2"></i>Shipment Details
        </h6>
        
        <div class="detail-cards">
            <!-- Customer Info -->
            <div class="detail-card">
                <div class="detail-icon">
                    <i class="fas fa-user text-primary"></i>
                </div>
                <div class="detail-content">
                    <label>Customer</label>
                    <span><?= esc($shipment['customer']) ?></span>
                </div>
            </div>

            <!-- Courier Info -->
            <div class="detail-card">
                <div class="detail-icon">
                    <i class="fas fa-motorcycle text-info"></i>
                </div>
                <div class="detail-content">
                    <label>Courier</label>
                    <span><?= esc($shipment['courier']) ?></span>
                </div>
            </div>

            <!-- Vehicle Info -->
            <?php if (!empty($shipment['vehicle'])): ?>
            <div class="detail-card">
                <div class="detail-icon">
                    <i class="fas fa-truck text-secondary"></i>
                </div>
                <div class="detail-content">
                    <label>Vehicle</label>
                    <span><?= esc($shipment['vehicle']) ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- PO Number -->
            <?php if (!empty($shipment['po_number'])): ?>
            <div class="detail-card">
                <div class="detail-icon">
                    <i class="fas fa-file-alt text-warning"></i>
                </div>
                <div class="detail-content">
                    <label>PO Number</label>
                    <span><?= esc($shipment['po_number']) ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Detail Location -->
            <div class="detail-card">
                <div class="detail-icon">
                    <i class="fas fa-map-marker-alt text-danger"></i>
                </div>
                <div class="detail-content">
                    <label>Detail Location</label>
                    <span><?= !empty($shipment['detail_location']) ? esc($shipment['detail_location']) : '-' ?></span>
                </div>
            </div>

            <!-- Recipient -->
            <?php if (!empty($shipment['recipient'])): ?>
            <div class="detail-card">
                <div class="detail-icon">
                    <i class="fas fa-user-check text-success"></i>
                </div>
                <div class="detail-content">
                    <label>Recipient</label>
                    <span><?= esc($shipment['recipient']) ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Items Section -->
    <?php if (!empty($shipment['items'])): ?>
    <div class="items-section">
        <h6 class="section-title">
            <i class="fas fa-boxes me-2"></i>Items (<?= count($shipment['items']) ?>)
        </h6>
        
        <div class="items-list">
            <?php foreach ($shipment['items'] as $index => $item): ?>
            <div class="item-card">
                <div class="item-number"><?= $index + 1 ?></div>
                <div class="item-details">
                    <div class="item-name"><?= esc($item['name']) ?></div>
                    <div class="item-quantity">
                        <span class="quantity"><?= esc($item['quantity']) ?></span>
                        <span class="unit"><?= esc($item['unit']) ?></span>
                    </div>
                    <?php if (!empty($item['notes'])): ?>
                    <div class="item-notes">
                        <small class="text-muted"><?= esc($item['notes']) ?></small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Notes Section -->
    <?php if (!empty($shipment['notes'])): ?>
    <div class="notes-section">
        <h6 class="section-title">
            <i class="fas fa-sticky-note me-2"></i>Notes
        </h6>
        <div class="notes-content">
            <?= nl2br(esc($shipment['notes'])) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="actions-section">
        <div class="action-buttons">
            <button class="btn btn-outline-primary" onclick="shareTracking()">
                <i class="fas fa-share me-2"></i>Share
            </button>
            <button class="btn btn-outline-secondary" onclick="refreshTracking()">
                <i class="fas fa-sync me-2"></i>Refresh
            </button>
            <button class="btn btn-primary" onclick="contactSupport()">
                <i class="fas fa-headset me-2"></i>Support
            </button>
        </div>
    </div>
</div>

<style>
.mobile-tracking-container {
    padding: 1rem;
    max-width: 600px;
    margin: 0 auto;
}

.tracking-header {
    margin-bottom: 1.5rem;
}

.status-card {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
    padding: 1.5rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow-lg);
}

.status-icon {
    flex-shrink: 0;
}

.status-info {
    flex-grow: 1;
}

.status-text {
    margin: 0 0 0.5rem 0;
    font-weight: 600;
}

.status-date {
    opacity: 0.9;
    font-size: 0.875rem;
}

.details-section,
.items-section,
.notes-section,
.actions-section {
    margin-bottom: 2rem;
}

.section-title {
    color: var(--gray-700);
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--gray-200);
}

.detail-cards {
    display: grid;
    gap: 0.75rem;
}

.detail-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 0.75rem;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-fast);
}

.detail-card:hover {
    box-shadow: var(--shadow);
}

.detail-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.detail-content {
    flex-grow: 1;
}

.detail-content label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.detail-content span {
    display: block;
    font-weight: 500;
    color: var(--gray-900);
}

.items-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.item-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 0.75rem;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    box-shadow: var(--shadow-sm);
}

.item-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.item-details {
    flex-grow: 1;
}

.item-name {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.item-quantity {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.quantity {
    font-weight: 600;
    color: var(--primary-color);
}

.unit {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.item-notes {
    margin-top: 0.5rem;
}

.notes-section .notes-content {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: 0.75rem;
    padding: 1rem;
    font-size: 0.875rem;
    line-height: 1.6;
}

.action-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.action-buttons .btn {
    padding: 0.75rem;
    font-weight: 500;
}

.action-buttons .btn:last-child {
    grid-column: 1 / -1;
}

/* Mobile optimizations */
@media (max-width: 575.98px) {
    .mobile-tracking-container {
        padding: 0.75rem;
    }
    
    .status-card {
        padding: 1rem;
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    .detail-card {
        padding: 0.75rem;
        gap: 0.75rem;
    }
    
    .detail-icon {
        width: 35px;
        height: 35px;
    }
    
    .item-card {
        padding: 0.75rem;
        gap: 0.75rem;
    }
    
    .item-number {
        width: 25px;
        height: 25px;
        font-size: 0.75rem;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .detail-card,
    .item-card {
        background: var(--gray-800);
        border-color: var(--gray-700);
        color: var(--gray-100);
    }
    
    .detail-content span {
        color: var(--gray-100);
    }
    
    .notes-content {
        background: var(--gray-800);
        border-color: var(--gray-700);
        color: var(--gray-100);
    }
}
</style>

<script>
// Share tracking information
function shareTracking() {
    const shipmentId = '<?= esc($shipment['shipment_id']) ?>';
    const url = window.location.href;
    const text = `Track shipment ${shipmentId}: ${url}`;
    
    if (navigator.share) {
        navigator.share({
            title: 'Shipment Tracking',
            text: text,
            url: url
        }).catch(console.error);
    } else {
        // Fallback to clipboard
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                if (window.app && window.app.showToast) {
                    window.app.showToast('Tracking link copied to clipboard', 'success');
                }
            });
        }
    }
}

// Refresh tracking data
function refreshTracking() {
    if (window.app && window.app.showLoading) {
        window.app.showLoading('Refreshing...');
    }
    
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Contact support
function contactSupport() {
    const shipmentId = '<?= esc($shipment['shipment_id']) ?>';
    const subject = `Support Request - Shipment ${shipmentId}`;
    const body = `Hello, I need assistance with shipment ${shipmentId}. Please help me with:`;
    
    const mailtoLink = `mailto:support@company.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    window.location.href = mailtoLink;
}

// Auto-refresh every 5 minutes
setInterval(() => {
    if (document.visibilityState === 'visible') {
        refreshTracking();
    }
}, 5 * 60 * 1000);
</script>

<?= $this->endsection() ?>