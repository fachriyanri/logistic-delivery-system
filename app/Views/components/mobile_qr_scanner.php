<?php
/**
 * Mobile QR Scanner Component
 * Provides camera-based QR code scanning for mobile devices
 */

$scannerId = $scannerId ?? 'qr-scanner-' . uniqid();
$targetInput = $targetInput ?? '';
$onScanSuccess = $onScanSuccess ?? 'handleQRScanSuccess';
$onScanError = $onScanError ?? 'handleQRScanError';
$showTorch = $showTorch ?? true;
$autoStart = $autoStart ?? false;
?>

<div class="mobile-qr-scanner" id="<?= $scannerId ?>">
    <!-- Scanner Interface -->
    <div class="qr-scanner-interface d-none">
        <div class="scanner-header">
            <h5 class="scanner-title">
                <i class="fas fa-qrcode me-2"></i>Scan QR Code
            </h5>
            <button type="button" class="btn-close scanner-close" aria-label="Close"></button>
        </div>
        
        <div class="scanner-body">
            <!-- Camera Preview -->
            <div class="camera-preview-container">
                <video class="camera-preview" autoplay muted playsinline></video>
                <canvas class="scanner-canvas d-none"></canvas>
                
                <!-- Scan Region Overlay -->
                <div class="scan-overlay">
                    <div class="scan-region">
                        <div class="scan-corners">
                            <div class="corner top-left"></div>
                            <div class="corner top-right"></div>
                            <div class="corner bottom-left"></div>
                            <div class="corner bottom-right"></div>
                        </div>
                        <div class="scan-line"></div>
                    </div>
                </div>
                
                <!-- Status Messages -->
                <div class="scanner-status">
                    <div class="status-message" id="scanner-message">
                        Position QR code within the frame
                    </div>
                </div>
            </div>
            
            <!-- Scanner Controls -->
            <div class="scanner-controls">
                <?php if ($showTorch): ?>
                <button type="button" class="btn btn-outline-light torch-toggle" id="torch-toggle">
                    <i class="fas fa-flashlight"></i>
                    <span class="torch-text">Torch</span>
                </button>
                <?php endif; ?>
                
                <button type="button" class="btn btn-outline-light camera-switch" id="camera-switch">
                    <i class="fas fa-camera-rotate"></i>
                    <span>Switch</span>
                </button>
                
                <button type="button" class="btn btn-danger scanner-stop" id="scanner-stop">
                    <i class="fas fa-stop me-2"></i>Stop
                </button>
            </div>
        </div>
    </div>
    
    <!-- Trigger Button -->
    <button type="button" class="btn btn-primary scanner-trigger" id="scanner-trigger">
        <i class="fas fa-qrcode me-2"></i>Scan QR Code
    </button>
    
    <!-- Manual Input Fallback -->
    <div class="manual-input-fallback d-none mt-3">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Enter code manually" id="manual-input">
            <button class="btn btn-outline-secondary" type="button" id="manual-submit">
                <i class="fas fa-check"></i>
            </button>
        </div>
        <small class="text-muted">Camera not available? Enter the code manually.</small>
    </div>
</div>

<style>
.mobile-qr-scanner {
    position: relative;
}

.qr-scanner-interface {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #000;
    z-index: 9999;
    display: flex;
    flex-direction: column;
}

.scanner-header {
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 10;
}

.scanner-title {
    margin: 0;
    font-size: 1.125rem;
}

.btn-close {
    filter: invert(1);
}

.scanner-body {
    flex: 1;
    position: relative;
    overflow: hidden;
}

.camera-preview-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.camera-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.scanner-canvas {
    position: absolute;
    top: 0;
    left: 0;
}

.scan-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.scan-region {
    width: 250px;
    height: 250px;
    position: relative;
    background: transparent;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
}

.scan-corners {
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
}

.corner {
    position: absolute;
    width: 20px;
    height: 20px;
    border: 3px solid #fff;
}

.corner.top-left {
    top: 0;
    left: 0;
    border-right: none;
    border-bottom: none;
    border-top-left-radius: 8px;
}

.corner.top-right {
    top: 0;
    right: 0;
    border-left: none;
    border-bottom: none;
    border-top-right-radius: 8px;
}

.corner.bottom-left {
    bottom: 0;
    left: 0;
    border-right: none;
    border-top: none;
    border-bottom-left-radius: 8px;
}

.corner.bottom-right {
    bottom: 0;
    right: 0;
    border-left: none;
    border-top: none;
    border-bottom-right-radius: 8px;
}

.scan-line {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #fff, transparent);
    animation: scanLine 2s linear infinite;
}

@keyframes scanLine {
    0% { transform: translateY(0); }
    100% { transform: translateY(246px); }
}

.scanner-status {
    position: absolute;
    bottom: 100px;
    left: 0;
    right: 0;
    text-align: center;
    z-index: 10;
}

.status-message {
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    display: inline-block;
    font-size: 0.875rem;
}

.scanner-controls {
    position: absolute;
    bottom: 20px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    gap: 1rem;
    padding: 0 1rem;
    z-index: 10;
}

.scanner-controls .btn {
    min-width: 60px;
    border-radius: 50px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    padding: 0.75rem;
    font-size: 0.75rem;
}

.scanner-controls .btn i {
    font-size: 1.25rem;
}

.torch-toggle.active {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

/* Mobile optimizations */
@media (max-width: 767.98px) {
    .scan-region {
        width: 200px;
        height: 200px;
    }
    
    .scanner-controls {
        bottom: 10px;
        gap: 0.5rem;
    }
    
    .scanner-controls .btn {
        min-width: 50px;
        padding: 0.5rem;
        font-size: 0.7rem;
    }
}

/* Landscape orientation */
@media (orientation: landscape) and (max-height: 500px) {
    .scan-region {
        width: 150px;
        height: 150px;
    }
    
    .scanner-status {
        bottom: 60px;
    }
    
    .scanner-controls {
        bottom: 5px;
    }
}
</style>

<script>
class MobileQRScanner {
    constructor(scannerId, options = {}) {
        this.scannerId = scannerId;
        this.container = document.getElementById(scannerId);
        this.options = {
            targetInput: options.targetInput || '',
            onScanSuccess: options.onScanSuccess || 'handleQRScanSuccess',
            onScanError: options.onScanError || 'handleQRScanError',
            showTorch: options.showTorch !== false,
            autoStart: options.autoStart || false,
            scanFrequency: options.scanFrequency || 10,
            ...options
        };
        
        this.isScanning = false;
        this.stream = null;
        this.video = null;
        this.canvas = null;
        this.context = null;
        this.scanInterval = null;
        this.currentFacingMode = 'environment';
        this.torchEnabled = false;
        
        this.init();
    }
    
    init() {
        this.setupElements();
        this.setupEventListeners();
        
        if (this.options.autoStart) {
            this.startScanner();
        }
    }
    
    setupElements() {
        this.interface = this.container.querySelector('.qr-scanner-interface');
        this.trigger = this.container.querySelector('.scanner-trigger');
        this.video = this.container.querySelector('.camera-preview');
        this.canvas = this.container.querySelector('.scanner-canvas');
        this.context = this.canvas.getContext('2d');
        this.statusMessage = this.container.querySelector('#scanner-message');
        this.torchToggle = this.container.querySelector('#torch-toggle');
        this.cameraSwitch = this.container.querySelector('#camera-switch');
        this.stopButton = this.container.querySelector('#scanner-stop');
        this.closeButton = this.container.querySelector('.scanner-close');
        this.manualInput = this.container.querySelector('#manual-input');
        this.manualSubmit = this.container.querySelector('#manual-submit');
        this.manualFallback = this.container.querySelector('.manual-input-fallback');
    }
    
    setupEventListeners() {
        this.trigger.addEventListener('click', () => this.startScanner());
        this.stopButton.addEventListener('click', () => this.stopScanner());
        this.closeButton.addEventListener('click', () => this.stopScanner());
        
        if (this.torchToggle) {
            this.torchToggle.addEventListener('click', () => this.toggleTorch());
        }
        
        if (this.cameraSwitch) {
            this.cameraSwitch.addEventListener('click', () => this.switchCamera());
        }
        
        if (this.manualSubmit) {
            this.manualSubmit.addEventListener('click', () => this.handleManualInput());
        }
        
        if (this.manualInput) {
            this.manualInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.handleManualInput();
                }
            });
        }
    }
    
    async startScanner() {
        try {
            // Check camera support
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                this.showManualFallback();
                return;
            }
            
            this.showInterface();
            this.updateStatus('Starting camera...');
            
            // Request camera access
            const constraints = {
                video: {
                    facingMode: this.currentFacingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };
            
            this.stream = await navigator.mediaDevices.getUserMedia(constraints);
            this.video.srcObject = this.stream;
            
            // Wait for video to load
            await new Promise((resolve) => {
                this.video.onloadedmetadata = resolve;
            });
            
            // Setup canvas
            this.canvas.width = this.video.videoWidth;
            this.canvas.height = this.video.videoHeight;
            
            this.isScanning = true;
            this.updateStatus('Position QR code within the frame');
            this.startScanLoop();
            
            // Add haptic feedback
            this.vibrate([50]);
            
        } catch (error) {
            console.error('Camera access failed:', error);
            this.updateStatus('Camera access denied');
            this.showManualFallback();
        }
    }
    
    stopScanner() {
        this.isScanning = false;
        
        if (this.scanInterval) {
            clearInterval(this.scanInterval);
            this.scanInterval = null;
        }
        
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        
        this.hideInterface();
        this.vibrate([100]);
    }
    
    startScanLoop() {
        this.scanInterval = setInterval(() => {
            if (this.isScanning && this.video.readyState === this.video.HAVE_ENOUGH_DATA) {
                this.scanFrame();
            }
        }, 1000 / this.options.scanFrequency);
    }
    
    scanFrame() {
        try {
            // Draw video frame to canvas
            this.context.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
            
            // Get image data
            const imageData = this.context.getImageData(0, 0, this.canvas.width, this.canvas.height);
            
            // Try to decode QR code
            const qrCode = this.decodeQRCode(imageData);
            
            if (qrCode) {
                this.handleScanSuccess(qrCode);
            }
            
        } catch (error) {
            console.warn('Scan frame error:', error);
        }
    }
    
    decodeQRCode(imageData) {
        // This is a simplified QR detection
        // In a real implementation, you would use a library like jsQR
        
        // For now, we'll simulate QR detection for demo purposes
        // You should integrate with jsQR or similar library
        
        if (typeof jsQR !== 'undefined') {
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            return code ? code.data : null;
        }
        
        // Fallback: return null (no QR detected)
        return null;
    }
    
    handleScanSuccess(qrData) {
        this.isScanning = false;
        this.updateStatus('QR Code detected!');
        
        // Success vibration
        this.vibrate([200]);
        
        // Fill target input if specified
        if (this.options.targetInput) {
            const input = document.querySelector(this.options.targetInput);
            if (input) {
                input.value = qrData;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
        
        // Call success callback
        if (typeof window[this.options.onScanSuccess] === 'function') {
            window[this.options.onScanSuccess](qrData, this);
        }
        
        // Auto-close after success
        setTimeout(() => {
            this.stopScanner();
        }, 1000);
    }
    
    handleScanError(error) {
        console.error('QR Scan error:', error);
        this.updateStatus('Scan failed. Try again.');
        
        // Error vibration
        this.vibrate([100, 100, 100]);
        
        // Call error callback
        if (typeof window[this.options.onScanError] === 'function') {
            window[this.options.onScanError](error, this);
        }
    }
    
    handleManualInput() {
        const value = this.manualInput.value.trim();
        if (value) {
            this.handleScanSuccess(value);
        }
    }
    
    async toggleTorch() {
        if (!this.stream) return;
        
        try {
            const track = this.stream.getVideoTracks()[0];
            const capabilities = track.getCapabilities();
            
            if (capabilities.torch) {
                this.torchEnabled = !this.torchEnabled;
                await track.applyConstraints({
                    advanced: [{ torch: this.torchEnabled }]
                });
                
                this.torchToggle.classList.toggle('active', this.torchEnabled);
                this.vibrate([50]);
            }
        } catch (error) {
            console.warn('Torch control failed:', error);
        }
    }
    
    async switchCamera() {
        if (!this.stream) return;
        
        try {
            // Stop current stream
            this.stream.getTracks().forEach(track => track.stop());
            
            // Switch facing mode
            this.currentFacingMode = this.currentFacingMode === 'environment' ? 'user' : 'environment';
            
            // Restart with new camera
            await this.startScanner();
            this.vibrate([50]);
            
        } catch (error) {
            console.warn('Camera switch failed:', error);
            this.updateStatus('Camera switch failed');
        }
    }
    
    showInterface() {
        this.interface.classList.remove('d-none');
        document.body.style.overflow = 'hidden';
    }
    
    hideInterface() {
        this.interface.classList.add('d-none');
        document.body.style.overflow = '';
        this.hideManualFallback();
    }
    
    showManualFallback() {
        this.manualFallback.classList.remove('d-none');
        this.updateStatus('Camera not available');
    }
    
    hideManualFallback() {
        this.manualFallback.classList.add('d-none');
    }
    
    updateStatus(message) {
        if (this.statusMessage) {
            this.statusMessage.textContent = message;
        }
    }
    
    vibrate(pattern) {
        if ('vibrate' in navigator) {
            navigator.vibrate(pattern);
        }
    }
}

// Initialize scanner for this component
document.addEventListener('DOMContentLoaded', () => {
    const scanner = new MobileQRScanner('<?= $scannerId ?>', {
        targetInput: '<?= $targetInput ?>',
        onScanSuccess: '<?= $onScanSuccess ?>',
        onScanError: '<?= $onScanError ?>',
        showTorch: <?= $showTorch ? 'true' : 'false' ?>,
        autoStart: <?= $autoStart ? 'true' : 'false' ?>
    });
    
    // Store reference for external access
    window['scanner_<?= $scannerId ?>'] = scanner;
});

// Default callback functions
function handleQRScanSuccess(qrData, scanner) {
    console.log('QR Code scanned:', qrData);
    
    if (window.app && window.app.showToast) {
        window.app.showToast('QR Code scanned successfully!', 'success');
    }
    
    // Validate and process QR data
    fetch('<?= base_url('api/qr/validate') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ qr_data: qrData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            // Handle valid QR code
            if (data.type === 'shipment_tracking') {
                window.location.href = `<?= base_url('pengiriman/view/') ?>${data.shipment_id}`;
            }
        } else {
            if (window.app && window.app.showToast) {
                window.app.showToast('Invalid QR code format', 'error');
            }
        }
    })
    .catch(error => {
        console.error('QR validation failed:', error);
        if (window.app && window.app.showToast) {
            window.app.showToast('Failed to validate QR code', 'error');
        }
    });
}

function handleQRScanError(error, scanner) {
    console.error('QR Scan error:', error);
    
    if (window.app && window.app.showToast) {
        window.app.showToast('QR scan failed. Please try again.', 'error');
    }
}
</script>