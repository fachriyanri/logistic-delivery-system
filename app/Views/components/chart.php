<?php
/**
 * Chart Component
 * 
 * @param string $id - Chart canvas ID
 * @param string $type - Chart type (line, bar, pie, doughnut, etc.)
 * @param array $data - Chart data
 * @param array $options - Chart options
 * @param string $title - Chart title
 * @param string $height - Chart height
 * @param string $class - Additional CSS classes
 */

$id = $id ?? 'chart-' . uniqid();
$type = $type ?? 'line';
$data = $data ?? [];
$options = $options ?? [];
$title = $title ?? '';
$height = $height ?? '400px';
$class = $class ?? '';

$chartClass = "chart-container";
if ($class) {
    $chartClass .= " {$class}";
}

// Default chart options
$defaultOptions = [
    'responsive' => true,
    'maintainAspectRatio' => false,
    'plugins' => [
        'legend' => [
            'position' => 'top'
        ],
        'title' => [
            'display' => !empty($title),
            'text' => $title
        ]
    ]
];

// Merge with provided options
$chartOptions = array_merge_recursive($defaultOptions, $options);
?>

<div class="<?= $chartClass ?>">
    <?php if ($title && !isset($options['plugins']['title']['display'])): ?>
    <div class="chart-title mb-3">
        <h5 class="mb-0"><?= esc($title) ?></h5>
    </div>
    <?php endif; ?>
    
    <div class="chart-wrapper" style="height: <?= $height ?>; position: relative;">
        <canvas id="<?= $id ?>" 
                width="400" 
                height="200"
                style="display: block; box-sizing: border-box; height: 100%; width: 100%;">
        </canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('<?= $id ?>');
    if (!ctx) return;
    
    const chartData = <?= json_encode($data) ?>;
    const chartOptions = <?= json_encode($chartOptions) ?>;
    
    // Create the chart
    const chart = new Chart(ctx, {
        type: '<?= $type ?>',
        data: chartData,
        options: chartOptions
    });
    
    // Store chart instance for external access
    window.charts = window.charts || {};
    window.charts['<?= $id ?>'] = chart;
});
</script>