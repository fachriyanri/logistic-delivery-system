<?php
/**
 * Badge Component
 * 
 * @param string $text - Badge text
 * @param string $variant - Badge variant (primary, secondary, success, etc.)
 * @param string $size - Badge size (sm, lg)
 * @param string $icon - Icon class
 * @param string $class - Additional CSS classes
 * @param bool $outline - Use outline style
 * @param bool $pill - Use pill style
 */

$text = $text ?? '';
$variant = $variant ?? 'primary';
$size = $size ?? '';
$icon = $icon ?? '';
$class = $class ?? '';
$outline = $outline ?? false;
$pill = $pill ?? false;

$badgeClass = $outline ? "badge badge-outline text-{$variant} border-{$variant}" : "badge bg-{$variant}";

if ($size) {
    $badgeClass .= " badge-{$size}";
}

if ($pill) {
    $badgeClass .= " rounded-pill";
}

if ($class) {
    $badgeClass .= " {$class}";
}
?>

<span class="<?= $badgeClass ?>">
    <?php if ($icon): ?>
        <i class="<?= $icon ?> me-1"></i>
    <?php endif; ?>
    <?= esc($text) ?>
</span>