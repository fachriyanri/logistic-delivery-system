<?php
/**
 * Skeleton Loading Component
 * 
 * @param string $type - Skeleton type (text, title, card, table, etc.)
 * @param int $lines - Number of lines for text skeleton
 * @param string $height - Height for custom skeleton
 * @param string $width - Width for custom skeleton
 * @param string $class - Additional CSS classes
 */

$type = $type ?? 'text';
$lines = $lines ?? 3;
$height = $height ?? '';
$width = $width ?? '';
$class = $class ?? '';

$skeletonClass = "skeleton";
if ($class) {
    $skeletonClass .= " {$class}";
}
?>

<?php if ($type === 'text'): ?>
    <div class="skeleton-text-container">
        <?php for ($i = 0; $i < $lines; $i++): ?>
            <div class="<?= $skeletonClass ?> skeleton-text" 
                 style="width: <?= $i === $lines - 1 ? '60%' : '100%' ?>"></div>
        <?php endfor; ?>
    </div>

<?php elseif ($type === 'title'): ?>
    <div class="<?= $skeletonClass ?> skeleton-title"></div>

<?php elseif ($type === 'card'): ?>
    <div class="card">
        <div class="card-body">
            <div class="<?= $skeletonClass ?> skeleton-title mb-3"></div>
            <div class="skeleton-text-container">
                <div class="<?= $skeletonClass ?> skeleton-text"></div>
                <div class="<?= $skeletonClass ?> skeleton-text"></div>
                <div class="<?= $skeletonClass ?> skeleton-text" style="width: 70%"></div>
            </div>
        </div>
    </div>

<?php elseif ($type === 'table'): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><div class="<?= $skeletonClass ?> skeleton-text"></div></th>
                    <th><div class="<?= $skeletonClass ?> skeleton-text"></div></th>
                    <th><div class="<?= $skeletonClass ?> skeleton-text"></div></th>
                    <th><div class="<?= $skeletonClass ?> skeleton-text"></div></th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < 5; $i++): ?>
                <tr>
                    <td><div class="<?= $skeletonClass ?> skeleton-text"></div></td>
                    <td><div class="<?= $skeletonClass ?> skeleton-text"></div></td>
                    <td><div class="<?= $skeletonClass ?> skeleton-text"></div></td>
                    <td><div class="<?= $skeletonClass ?> skeleton-text"></div></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($type === 'stats'): ?>
    <div class="row">
        <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="<?= $skeletonClass ?>" 
                             style="width: 3rem; height: 3rem; border-radius: 0.75rem; margin-right: 1rem;"></div>
                        <div class="flex-grow-1">
                            <div class="<?= $skeletonClass ?> skeleton-title mb-2"></div>
                            <div class="<?= $skeletonClass ?> skeleton-text" style="width: 60%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>

<?php elseif ($type === 'avatar'): ?>
    <div class="<?= $skeletonClass ?>" 
         style="width: 2.5rem; height: 2.5rem; border-radius: 50%;"></div>

<?php elseif ($type === 'button'): ?>
    <div class="<?= $skeletonClass ?>" 
         style="width: 100px; height: 38px; border-radius: 0.375rem;"></div>

<?php elseif ($type === 'image'): ?>
    <div class="<?= $skeletonClass ?>" 
         style="width: <?= $width ?: '100%' ?>; height: <?= $height ?: '200px' ?>; border-radius: 0.375rem;"></div>

<?php else: ?>
    <!-- Custom skeleton -->
    <div class="<?= $skeletonClass ?>" 
         style="<?= $height ? 'height: ' . $height . ';' : '' ?><?= $width ? 'width: ' . $width . ';' : '' ?>"></div>

<?php endif; ?>