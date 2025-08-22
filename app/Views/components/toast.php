<?php
/**
 * Toast Notification Component
 * 
 * @param string $id - Toast ID
 * @param string $title - Toast title
 * @param string $message - Toast message
 * @param string $type - Toast type (success, error, warning, info)
 * @param int $delay - Auto-hide delay in milliseconds
 * @param bool $autohide - Whether to auto-hide the toast
 * @param string $class - Additional CSS classes
 */

$id = $id ?? 'toast-' . uniqid();
$title = $title ?? '';
$message = $message ?? '';
$type = $type ?? 'info';
$delay = $delay ?? 5000;
$autohide = $autohide ?? true;
$class = $class ?? '';

$toastClass = "toast";
if ($class) {
    $toastClass .= " {$class}";
}

$iconClass = match($type) {
    'success' => 'fas fa-check-circle text-success',
    'error' => 'fas fa-exclamation-circle text-danger',
    'warning' => 'fas fa-exclamation-triangle text-warning',
    'info' => 'fas fa-info-circle text-info',
    default => 'fas fa-info-circle text-info'
};

$bgClass = match($type) {
    'success' => 'bg-success-subtle border-success',
    'error' => 'bg-danger-subtle border-danger',
    'warning' => 'bg-warning-subtle border-warning',
    'info' => 'bg-info-subtle border-info',
    default => 'bg-light border-secondary'
};
?>

<div id="<?= $id ?>" 
     class="<?= $toastClass ?> <?= $bgClass ?>" 
     role="alert" 
     aria-live="assertive" 
     aria-atomic="true"
     data-bs-delay="<?= $delay ?>"
     data-bs-autohide="<?= $autohide ? 'true' : 'false' ?>">
    
    <?php if ($title): ?>
    <div class="toast-header">
        <i class="<?= $iconClass ?> me-2"></i>
        <strong class="me-auto"><?= esc($title) ?></strong>
        <small class="text-muted"><?= date('H:i') ?></small>
        <button type="button" 
                class="btn-close" 
                data-bs-dismiss="toast" 
                aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <div class="toast-body d-flex align-items-center">
        <?php if (!$title): ?>
        <i class="<?= $iconClass ?> me-3 flex-shrink-0"></i>
        <?php endif; ?>
        <div class="flex-grow-1">
            <?= esc($message) ?>
        </div>
        <?php if (!$title): ?>
        <button type="button" 
                class="btn-close ms-3" 
                data-bs-dismiss="toast" 
                aria-label="Close"></button>
        <?php endif; ?>
    </div>
</div>