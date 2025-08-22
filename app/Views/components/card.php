<?php
/**
 * Card Component
 * 
 * @param string $title - Card title
 * @param string $content - Card content (HTML)
 * @param string $class - Additional CSS classes
 * @param array $actions - Array of action buttons
 * @param string $icon - Icon class for title
 * @param bool $collapsible - Whether card is collapsible
 */

$title = $title ?? '';
$content = $content ?? '';
$class = $class ?? '';
$actions = $actions ?? [];
$icon = $icon ?? '';
$collapsible = $collapsible ?? false;
$cardId = $cardId ?? 'card-' . uniqid();
?>

<div class="card <?= $class ?>">
    <?php if ($title || !empty($actions)): ?>
    <div class="card-header <?= $collapsible ? 'cursor-pointer' : '' ?>" 
         <?= $collapsible ? 'data-bs-toggle="collapse" data-bs-target="#' . $cardId . '-body"' : '' ?>>
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <?php if ($icon): ?>
                    <i class="<?= $icon ?> me-2"></i>
                <?php endif; ?>
                <?= esc($title) ?>
            </h5>
            
            <?php if (!empty($actions)): ?>
            <div class="card-actions">
                <?php foreach ($actions as $action): ?>
                    <a href="<?= $action['url'] ?>" 
                       class="btn <?= $action['class'] ?? 'btn-sm btn-outline-primary' ?>"
                       <?= isset($action['target']) ? 'target="' . $action['target'] . '"' : '' ?>>
                        <?php if (isset($action['icon'])): ?>
                            <i class="<?= $action['icon'] ?> me-1"></i>
                        <?php endif; ?>
                        <?= esc($action['title']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($collapsible): ?>
            <button class="btn btn-sm btn-ghost" type="button">
                <i class="fas fa-chevron-down"></i>
            </button>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="card-body <?= $collapsible ? 'collapse show' : '' ?>" 
         <?= $collapsible ? 'id="' . $cardId . '-body"' : '' ?>>
        <?= $content ?>
    </div>
</div>