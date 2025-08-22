<?php
/**
 * Status Badge Component
 * 
 * @param int $status - Status code
 * @param array $statusMap - Custom status mapping
 * @param string $class - Additional CSS classes
 */

$status = $status ?? 0;
$statusMap = $statusMap ?? [];
$class = $class ?? '';

// Default status mappings for shipments
$defaultStatusMap = [
    SHIPMENT_STATUS_PENDING => [
        'text' => 'Pending',
        'variant' => 'warning',
        'icon' => 'fas fa-clock'
    ],
    SHIPMENT_STATUS_IN_TRANSIT => [
        'text' => 'In Transit',
        'variant' => 'info',
        'icon' => 'fas fa-truck'
    ],
    SHIPMENT_STATUS_DELIVERED => [
        'text' => 'Delivered',
        'variant' => 'success',
        'icon' => 'fas fa-check-circle'
    ],
    SHIPMENT_STATUS_CANCELLED => [
        'text' => 'Cancelled',
        'variant' => 'danger',
        'icon' => 'fas fa-times-circle'
    ]
];

// Merge custom status map with default
$statusMap = array_merge($defaultStatusMap, $statusMap);

// Get status info
$statusInfo = $statusMap[$status] ?? [
    'text' => 'Unknown',
    'variant' => 'secondary',
    'icon' => 'fas fa-question-circle'
];

$badgeClass = "badge bg-{$statusInfo['variant']}";
if ($class) {
    $badgeClass .= " {$class}";
}
?>

<span class="<?= $badgeClass ?>">
    <i class="<?= $statusInfo['icon'] ?> me-1"></i>
    <?= esc($statusInfo['text']) ?>
</span>