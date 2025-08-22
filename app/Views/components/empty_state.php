<?php
/**
 * Empty State Component
 * 
 * @param string $title - Empty state title
 * @param string $description - Empty state description
 * @param string $icon - Icon class
 * @param array $actions - Array of action buttons
 * @param string $class - Additional CSS classes
 */

$title = $title ?? 'No data available';
$description = $description ?? 'There are no items to display at the moment.';
$icon = $icon ?? 'fas fa-inbox';
$actions = $actions ?? [];
$class = $class ?? '';

$emptyClass = "empty-state";
if ($class) {
    $emptyClass .= " {$class}";
}
?>

<div class="<?= $emptyClass ?>">
    <div class="empty-state-icon">
        <i class="<?= $icon ?>"></i>
    </div>
    
    <div class="empty-state-title">
        <?= esc($title) ?>
    </div>
    
    <div class="empty-state-description">
        <?= esc($description) ?>
    </div>
    
    <?php if (!empty($actions)): ?>
    <div class="empty-state-actions">
        <?php foreach ($actions as $action): ?>
            <?= view('components/button', [
                'text' => $action['text'],
                'url' => $action['url'] ?? null,
                'variant' => $action['variant'] ?? 'primary',
                'icon' => $action['icon'] ?? null,
                'class' => $action['class'] ?? ''
            ]) ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>