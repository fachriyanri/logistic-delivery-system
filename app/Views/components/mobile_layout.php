<?php
/**
 * Mobile Layout Component
 * Provides mobile-optimized layout patterns
 */

$mobileClass = $mobileClass ?? '';
$showActionBar = $showActionBar ?? false;
$actionButtons = $actionButtons ?? [];
$pullToRefresh = $pullToRefresh ?? false;
$swipeable = $swipeable ?? false;
?>

<div class="mobile-container <?= $mobileClass ?> <?= $pullToRefresh ? 'pull-to-refresh' : '' ?>" 
     <?= $pullToRefresh ? 'data-refresh-callback="refreshPage"' : '' ?>>
    
    <?php if ($pullToRefresh): ?>
    <div class="pull-indicator">
        <i class="fas fa-sync-alt"></i>
    </div>
    <?php endif; ?>
    
    <div class="mobile-content <?= $swipeable ? 'swipeable' : '' ?>">
        <?= $this->renderSection('mobile_content') ?>
    </div>
    
    <?php if ($showActionBar && !empty($actionButtons)): ?>
    <div class="mobile-action-bar d-lg-none">
        <?php foreach ($actionButtons as $button): ?>
        <button type="<?= $button['type'] ?? 'button' ?>" 
                class="btn <?= $button['class'] ?? 'btn-primary' ?>"
                <?= isset($button['onclick']) ? 'onclick="' . $button['onclick'] . '"' : '' ?>
                <?= isset($button['href']) ? 'onclick="window.location.href=\'' . $button['href'] . '\'"' : '' ?>>
            <?php if (isset($button['icon'])): ?>
            <i class="<?= $button['icon'] ?> me-2"></i>
            <?php endif; ?>
            <?= $button['text'] ?>
        </button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.mobile-container {
    position: relative;
    min-height: 100vh;
    overflow-x: hidden;
}

.mobile-content {
    padding-bottom: <?= $showActionBar ? '80px' : '0' ?>;
}

@media (min-width: 992px) {
    .mobile-content {
        padding-bottom: 0;
    }
}
</style>

<script>
function refreshPage() {
    showLoading('Refreshing...');
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}
</script>