<?php
/**
 * Button Component
 * 
 * @param string $text - Button text
 * @param string $url - Button URL (for links)
 * @param string $type - Button type (button, submit, reset)
 * @param string $variant - Button variant (primary, secondary, success, etc.)
 * @param string $size - Button size (sm, lg)
 * @param string $icon - Icon class
 * @param string $class - Additional CSS classes
 * @param array $attributes - Additional HTML attributes
 * @param bool $loading - Show loading state
 * @param string $loadingText - Loading text
 */

$text = $text ?? 'Button';
$url = $url ?? null;
$type = $type ?? 'button';
$variant = $variant ?? 'primary';
$size = $size ?? '';
$icon = $icon ?? '';
$class = $class ?? '';
$attributes = $attributes ?? [];
$loading = $loading ?? false;
$loadingText = $loadingText ?? 'Loading...';

$btnClass = "btn btn-{$variant}";
if ($size) {
    $btnClass .= " btn-{$size}";
}
if ($class) {
    $btnClass .= " {$class}";
}

$attributeString = '';
foreach ($attributes as $key => $value) {
    $attributeString .= " {$key}=\"" . esc($value) . "\"";
}
?>

<?php if ($url): ?>
    <a href="<?= $url ?>" class="<?= $btnClass ?>"<?= $attributeString ?>>
        <?php if ($icon && !$loading): ?>
            <i class="<?= $icon ?> me-2"></i>
        <?php endif; ?>
        
        <?php if ($loading): ?>
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            <?= esc($loadingText) ?>
        <?php else: ?>
            <?= esc($text) ?>
        <?php endif; ?>
    </a>
<?php else: ?>
    <button type="<?= $type ?>" class="<?= $btnClass ?>"<?= $attributeString ?>>
        <?php if ($icon && !$loading): ?>
            <i class="<?= $icon ?> me-2"></i>
        <?php endif; ?>
        
        <?php if ($loading): ?>
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            <span class="btn-text d-none"><?= esc($text) ?></span>
            <span class="loading-text"><?= esc($loadingText) ?></span>
        <?php else: ?>
            <span class="btn-text"><?= esc($text) ?></span>
        <?php endif; ?>
    </button>
<?php endif; ?>