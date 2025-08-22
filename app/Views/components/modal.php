<?php
/**
 * Modal Component
 * 
 * @param string $id - Modal ID
 * @param string $title - Modal title
 * @param string $content - Modal content (HTML)
 * @param array $buttons - Modal buttons configuration
 * @param string $size - Modal size (sm, lg, xl)
 * @param bool $centered - Center modal vertically
 * @param bool $scrollable - Make modal body scrollable
 * @param string $class - Additional CSS classes
 */

$id = $id ?? 'modal-' . uniqid();
$title = $title ?? '';
$content = $content ?? '';
$buttons = $buttons ?? [];
$size = $size ?? '';
$centered = $centered ?? false;
$scrollable = $scrollable ?? false;
$class = $class ?? '';

$modalClass = "modal fade";
if ($class) {
    $modalClass .= " {$class}";
}

$dialogClass = "modal-dialog";
if ($size) {
    $dialogClass .= " modal-{$size}";
}
if ($centered) {
    $dialogClass .= " modal-dialog-centered";
}
if ($scrollable) {
    $dialogClass .= " modal-dialog-scrollable";
}
?>

<div class="<?= $modalClass ?>" 
     id="<?= $id ?>" 
     tabindex="-1" 
     aria-labelledby="<?= $id ?>Label" 
     aria-hidden="true">
    <div class="<?= $dialogClass ?>">
        <div class="modal-content">
            <?php if ($title): ?>
            <div class="modal-header">
                <h5 class="modal-title" id="<?= $id ?>Label">
                    <?= esc($title) ?>
                </h5>
                <button type="button" 
                        class="btn-close" 
                        data-bs-dismiss="modal" 
                        aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <div class="modal-body">
                <?= $content ?>
            </div>
            
            <?php if (!empty($buttons)): ?>
            <div class="modal-footer">
                <?php foreach ($buttons as $button): ?>
                    <?php
                    $btnClass = $button['class'] ?? 'btn-secondary';
                    $btnAction = $button['action'] ?? '';
                    $btnText = $button['text'] ?? 'Button';
                    $btnIcon = $button['icon'] ?? '';
                    $btnDismiss = $button['dismiss'] ?? false;
                    ?>
                    <button type="button" 
                            class="btn <?= $btnClass ?>"
                            <?= $btnDismiss ? 'data-bs-dismiss="modal"' : '' ?>
                            <?= $btnAction ? 'onclick="' . esc($btnAction) . '"' : '' ?>>
                        <?php if ($btnIcon): ?>
                            <i class="<?= $btnIcon ?> me-2"></i>
                        <?php endif; ?>
                        <?= esc($btnText) ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>