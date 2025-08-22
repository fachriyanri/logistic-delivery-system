<?php
/**
 * Statistics Card Component
 * 
 * @param string $title - Card title
 * @param mixed $value - Statistics value
 * @param string $icon - Icon class
 * @param string $color - Color variant (primary, success, warning, etc.)
 * @param string $change - Change percentage (optional)
 * @param bool $positive - Whether change is positive
 * @param string $url - Link URL (optional)
 * @param string $class - Additional CSS classes
 */

$title = $title ?? '';
$value = $value ?? 0;
$icon = $icon ?? 'fas fa-chart-bar';
$color = $color ?? 'primary';
$change = $change ?? '';
$positive = $positive ?? true;
$url = $url ?? '';
$class = $class ?? '';

$cardClass = "card card-stats h-100";
if ($class) {
    $cardClass .= " {$class}";
}

$changeClass = $positive ? 'positive text-success' : 'negative text-danger';
$changeIcon = $positive ? 'fas fa-arrow-up' : 'fas fa-arrow-down';
?>

<?php if ($url): ?>
<a href="<?= $url ?>" class="text-decoration-none">
<?php endif; ?>

<div class="<?= $cardClass ?>">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="stats-icon bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> me-3">
                <i class="<?= $icon ?>"></i>
            </div>
            <div class="flex-grow-1">
                <div class="stats-number text-<?= $color ?>"><?= number_format($value) ?></div>
                <div class="stats-label"><?= esc($title) ?></div>
                
                <?php if ($change): ?>
                <div class="stats-change <?= $changeClass ?>">
                    <i class="<?= $changeIcon ?>"></i>
                    <?= esc($change) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($url): ?>
</a>
<?php endif; ?>