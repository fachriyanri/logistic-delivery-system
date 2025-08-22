<?php
/**
 * Mobile Data Card Component
 * Displays data in a mobile-friendly card format
 */

$data = $data ?? [];
$title = $title ?? '';
$subtitle = $subtitle ?? '';
$actions = $actions ?? [];
$swipeable = $swipeable ?? false;
$cardClass = $cardClass ?? '';
?>

<div class="mobile-data-card <?= $cardClass ?> <?= $swipeable ? 'swipe-item' : '' ?>">
    <?php if ($title || $subtitle): ?>
    <div class="mobile-data-header">
        <?php if ($title): ?>
        <h6 class="mobile-data-title mb-1"><?= esc($title) ?></h6>
        <?php endif; ?>
        <?php if ($subtitle): ?>
        <small class="text-muted"><?= esc($subtitle) ?></small>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <div class="mobile-data-body">
        <?php foreach ($data as $item): ?>
        <div class="mobile-data-row">
            <span class="mobile-data-label"><?= esc($item['label']) ?></span>
            <span class="mobile-data-value">
                <?php if (isset($item['badge'])): ?>
                <span class="badge <?= $item['badge']['class'] ?? 'bg-secondary' ?>">
                    <?= esc($item['value']) ?>
                </span>
                <?php elseif (isset($item['icon'])): ?>
                <i class="<?= $item['icon'] ?> me-1"></i><?= esc($item['value']) ?>
                <?php else: ?>
                <?= esc($item['value']) ?>
                <?php endif; ?>
            </span>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (!empty($actions)): ?>
    <div class="mobile-data-actions">
        <?php foreach ($actions as $action): ?>
        <a href="<?= $action['href'] ?? '#' ?>" 
           class="btn btn-sm <?= $action['class'] ?? 'btn-outline-primary' ?>"
           <?= isset($action['onclick']) ? 'onclick="' . $action['onclick'] . '"' : '' ?>>
            <?php if (isset($action['icon'])): ?>
            <i class="<?= $action['icon'] ?> me-1"></i>
            <?php endif; ?>
            <?= esc($action['text']) ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <?php if ($swipeable): ?>
    <div class="swipe-actions">
        <i class="fas fa-trash"></i>
    </div>
    <?php endif; ?>
</div>

<style>
.mobile-data-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-fast);
}

.mobile-data-card:hover {
    box-shadow: var(--shadow);
}

.mobile-data-header {
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--gray-100);
}

.mobile-data-title {
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.mobile-data-body {
    margin-bottom: 0.75rem;
}

.mobile-data-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--gray-100);
}

.mobile-data-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.mobile-data-label {
    font-weight: 500;
    color: var(--gray-700);
    font-size: 0.875rem;
    flex: 1;
}

.mobile-data-value {
    color: var(--gray-900);
    text-align: right;
    font-size: 0.875rem;
    font-weight: 500;
}

.mobile-data-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid var(--gray-100);
}

.mobile-data-actions .btn {
    flex: 1;
    min-width: 0;
}

/* Swipe functionality */
.swipe-item {
    position: relative;
    overflow: hidden;
    transform: translateX(0);
    transition: transform 0.3s ease;
}

.swipe-actions {
    position: absolute;
    top: 0;
    right: -80px;
    height: 100%;
    width: 80px;
    background: var(--danger-color);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    transition: right 0.3s ease;
}

.swipe-item.swiped {
    transform: translateX(-80px);
}

.swipe-item.swiped .swipe-actions {
    right: 0;
}

@media (min-width: 768px) {
    .mobile-data-card {
        display: none;
    }
}
</style>