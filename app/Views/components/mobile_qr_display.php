<?php
/**
 * Mobile QR Display Component
 * Displays QR codes in a mobile-optimized format
 */

$qrData = $qrData ?? [];
$title = $title ?? 'QR Code';
$subtitle = $subtitle ?? '';
$showDownload = $showDownload ?? true;
$showShare = $showShare ?? true;
$responsive = $responsive ?? true;
$size = $size ?? 'medium';
?>

<div class="mobile-qr-display <?= $responsive ? 'responsive' : '' ?>">
    <div class="qr-display-card">
        <!-- Header -->
        <div class="qr-display-header">
            <h5 class="qr-title"><?= esc($title) ?></h5>
            <?php if ($subtitle): ?>
            <p class="qr-subtitle text-muted"><?= esc($subtitle) ?></p>
            <?php endif; ?>
        </div>
        
        <!-- QR Code Display -->
        <div class="qr-display-body">
            <div class="qr-code-container <?= $size ?>">
                <?php if (isset($qrData['url'])): ?>
                <img src="<?= $qrData['url'] ?>" 
                     alt="QR Code" 
                     class="qr-code-image"
                     loading="lazy">
                <?php else: ?>
                <div class="qr-placeholder">
                    <i class="fas fa-qrcode fa-3x text-muted"></i>
                    <p class="mt-2 text-muted">QR Code not available</p>
                </div>
                <?php endif; ?>
                
                <!-- QR Code Info -->
                <div class="qr-info">
                    <?php if (isset($qrData['data'])): ?>
                    <small class="qr-data text-muted">
                        Data: <?= esc(substr($qrData['data'], 0, 50)) ?><?= strlen($qrData['data']) > 50 ? '...' : '' ?>
                    </small>
                    <?php endif; ?>
                    
                    <?php if (isset($qrData['verification_code'])): ?>
                    <div class="verification-code mt-2">
                        <strong>Verification Code: <?= esc($qrData['verification_code']) ?></strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="qr-display-actions">
            <?php if ($showDownload && isset($qrData['url'])): ?>
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="downloadQRCode('<?= $qrData['url'] ?>', '<?= $qrData['filename'] ?? 'qrcode.png' ?>')">
                <i class="fas fa-download me-2"></i>Download
            </button>
            <?php endif; ?>
            
            <?php if ($showShare && isset($qrData['mobile_url'])): ?>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="shareQRCode('<?= $qrData['mobile_url'] ?>')">
                <i class="fas fa-share me-2"></i>Share
            </button>
            <?php endif; ?>
            
            <button type="button" class="btn btn-outline-info btn-sm" onclick="enlargeQRCode('<?= $qrData['url'] ?? '' ?>')">
                <i class="fas fa-expand me-2"></i>Enlarge
            </button>
        </div>
    </div>
</div>

<!-- QR Code Enlargement Modal -->
<div class="modal fade" id="qrEnlargeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="enlargedQR" src="" alt="QR Code" class="img-fluid">
                <div class="mt-3">
                    <small class="text-muted">Scan with your device camera or QR code reader</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.mobile-qr-display {
    max-width: 100%;
}

.qr-display-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    text-align: center;
}

.qr-display-header {
    margin-bottom: 1.5rem;
}

.qr-title {
    margin: 0 0 0.5rem 0;
    font-weight: 600;
    color: var(--gray-900);
}

.qr-subtitle {
    margin: 0;
    font-size: 0.875rem;
}

.qr-display-body {
    margin-bottom: 1.5rem;
}

.qr-code-container {
    display: inline-block;
    position: relative;
}

.qr-code-container.small .qr-code-image {
    width: 150px;
    height: 150px;
}

.qr-code-container.medium .qr-code-image {
    width: 200px;
    height: 200px;
}

.qr-code-container.large .qr-code-image {
    width: 250px;
    height: 250px;
}

.qr-code-image {
    border: 1px solid var(--gray-200);
    border-radius: 0.5rem;
    padding: 0.5rem;
    background: white;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-fast);
}

.qr-code-image:hover {
    box-shadow: var(--shadow);
}

.qr-placeholder {
    width: 200px;
    height: 200px;
    border: 2px dashed var(--gray-300);
    border-radius: 0.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: var(--gray-50);
}

.qr-info {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
}

.qr-data {
    display: block;
    word-break: break-all;
    font-family: monospace;
    font-size: 0.75rem;
}

.verification-code {
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    display: inline-block;
}

.qr-display-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* Mobile optimizations */
@media (max-width: 575.98px) {
    .qr-display-card {
        padding: 1rem;
    }
    
    .qr-code-container.small .qr-code-image {
        width: 120px;
        height: 120px;
    }
    
    .qr-code-container.medium .qr-code-image {
        width: 150px;
        height: 150px;
    }
    
    .qr-code-container.large .qr-code-image {
        width: 180px;
        height: 180px;
    }
    
    .qr-placeholder {
        width: 150px;
        height: 150px;
    }
    
    .qr-display-actions {
        flex-direction: column;
    }
    
    .qr-display-actions .btn {
        width: 100%;
    }
}

/* Responsive behavior */
.mobile-qr-display.responsive {
    width: 100%;
}

.mobile-qr-display.responsive .qr-code-container {
    width: 100%;
    max-width: 300px;
}

.mobile-qr-display.responsive .qr-code-image {
    width: 100% !important;
    height: auto !important;
    aspect-ratio: 1;
}

.mobile-qr-display.responsive .qr-placeholder {
    width: 100% !important;
    height: auto !important;
    aspect-ratio: 1;
    max-width: 300px;
}

/* Print styles */
@media print {
    .qr-display-actions {
        display: none;
    }
    
    .qr-display-card {
        box-shadow: none;
        border: 1px solid #000;
    }
    
    .qr-code-image {
        box-shadow: none;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .qr-code-image {
        border: 2px solid #000;
    }
    
    .qr-placeholder {
        border: 2px solid #000;
    }
}
</style>

<script>
// Download QR Code functionality
function downloadQRCode(url, filename) {
    if (!url) {
        if (window.app && window.app.showToast) {
            window.app.showToast('QR code not available for download', 'error');
        }
        return;
    }
    
    // Create download link
    const link = document.createElement('a');
    link.href = url;
    link.download = filename || 'qrcode.png';
    link.target = '_blank';
    
    // Trigger download
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success message
    if (window.app && window.app.showToast) {
        window.app.showToast('QR code downloaded successfully', 'success');
    }
    
    // Add haptic feedback
    if ('vibrate' in navigator) {
        navigator.vibrate([50]);
    }
}

// Share QR Code functionality
async function shareQRCode(url) {
    if (!url) {
        if (window.app && window.app.showToast) {
            window.app.showToast('No URL to share', 'error');
        }
        return;
    }
    
    // Check if Web Share API is supported
    if (navigator.share) {
        try {
            await navigator.share({
                title: 'Shipment Tracking',
                text: 'Track your shipment with this QR code',
                url: url
            });
            
            if (window.app && window.app.showToast) {
                window.app.showToast('Shared successfully', 'success');
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Share failed:', error);
                fallbackShare(url);
            }
        }
    } else {
        fallbackShare(url);
    }
}

// Fallback share functionality
function fallbackShare(url) {
    // Copy to clipboard
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            if (window.app && window.app.showToast) {
                window.app.showToast('Link copied to clipboard', 'success');
            }
        }).catch(() => {
            showShareModal(url);
        });
    } else {
        showShareModal(url);
    }
}

// Show share modal with options
function showShareModal(url) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Share QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Share this link:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="${url}" readonly id="shareUrl">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyShareUrl()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="mailto:?subject=Shipment Tracking&body=Track your shipment: ${url}" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i>Email
                        </a>
                        <a href="sms:?body=Track your shipment: ${url}" class="btn btn-outline-success">
                            <i class="fas fa-sms me-2"></i>SMS
                        </a>
                        <a href="whatsapp://send?text=Track your shipment: ${url}" class="btn btn-outline-success">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Remove modal when hidden
    modal.addEventListener('hidden.bs.modal', () => {
        document.body.removeChild(modal);
    });
    
    // Copy URL function
    window.copyShareUrl = function() {
        const input = document.getElementById('shareUrl');
        input.select();
        document.execCommand('copy');
        
        if (window.app && window.app.showToast) {
            window.app.showToast('Link copied to clipboard', 'success');
        }
    };
}

// Enlarge QR Code functionality
function enlargeQRCode(url) {
    if (!url) {
        if (window.app && window.app.showToast) {
            window.app.showToast('QR code not available', 'error');
        }
        return;
    }
    
    const modal = document.getElementById('qrEnlargeModal');
    const img = document.getElementById('enlargedQR');
    
    if (modal && img) {
        img.src = url;
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}

// Auto-adjust QR size based on screen size
function adjustQRSize() {
    const containers = document.querySelectorAll('.qr-code-container');
    containers.forEach(container => {
        const screenWidth = window.innerWidth;
        
        if (screenWidth < 576) {
            container.classList.remove('medium', 'large');
            container.classList.add('small');
        } else if (screenWidth < 768) {
            container.classList.remove('small', 'large');
            container.classList.add('medium');
        } else {
            container.classList.remove('small', 'medium');
            container.classList.add('large');
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', adjustQRSize);
window.addEventListener('resize', adjustQRSize);
</script>