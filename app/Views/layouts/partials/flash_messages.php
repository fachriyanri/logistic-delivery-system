<?php
$session = session();
$flashTypes = ['success', 'error', 'warning', 'info'];
?>

<div class="flash-messages">
    <?php foreach ($flashTypes as $type): ?>
        <?php if ($session->getFlashdata($type)): ?>
            <?php
            $alertClass = match($type) {
                'success' => 'alert-success',
                'error' => 'alert-danger',
                'warning' => 'alert-warning',
                'info' => 'alert-info',
                default => 'alert-info'
            };
            
            $iconClass = match($type) {
                'success' => 'fas fa-check-circle',
                'error' => 'fas fa-exclamation-circle',
                'warning' => 'fas fa-exclamation-triangle',
                'info' => 'fas fa-info-circle',
                default => 'fas fa-info-circle'
            };
            ?>
            
            <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="<?= $iconClass ?> me-3 flex-shrink-0"></i>
                    <div class="flex-grow-1">
                        <?php 
                        $message = $session->getFlashdata($type);
                        if (is_array($message)): 
                        ?>
                            <ul class="mb-0">
                                <?php foreach ($message as $msg): ?>
                                    <li><?= esc($msg) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <?= esc($message) ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" 
                            class="btn-close" 
                            data-bs-dismiss="alert" 
                            aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Auto-hide flash messages after 5 seconds -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.flash-messages .alert');
    
    alerts.forEach(function(alert) {
        // Auto-hide after 5 seconds (except error messages)
        if (!alert.classList.contains('alert-danger')) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });
});
</script>