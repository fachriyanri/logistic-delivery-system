<?php
/**
 * Analytics Card Component
 * 
 * @param string $title - Card title
 * @param mixed $value - Main value to display
 * @param string $subtitle - Subtitle text
 * @param string $icon - Icon class
 * @param string $color - Color variant
 * @param array $trend - Trend data (value, direction, period)
 * @param string $url - Link URL
 * @param array $sparkline - Sparkline data points
 * @param string $class - Additional CSS classes
 */

$title = $title ?? '';
$value = $value ?? 0;
$subtitle = $subtitle ?? '';
$icon = $icon ?? 'fas fa-chart-bar';
$color = $color ?? 'primary';
$trend = $trend ?? [];
$url = $url ?? '';
$sparkline = $sparkline ?? [];
$class = $class ?? '';

$cardClass = "analytics-card h-100";
if ($class) {
    $cardClass .= " {$class}";
}

$trendDirection = $trend['direction'] ?? 'neutral';
$trendValue = $trend['value'] ?? '';
$trendPeriod = $trend['period'] ?? '';

$trendClass = match($trendDirection) {
    'up' => 'text-success',
    'down' => 'text-danger',
    default => 'text-muted'
};

$trendIcon = match($trendDirection) {
    'up' => 'fas fa-arrow-up',
    'down' => 'fas fa-arrow-down',
    default => 'fas fa-minus'
};

$sparklineId = 'sparkline-' . uniqid();
?>

<?php if ($url): ?>
<a href="<?= $url ?>" class="text-decoration-none">
<?php endif; ?>

<div class="card <?= $cardClass ?>" data-color="<?= $color ?>">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
            <div class="flex-grow-1">
                <!-- Header -->
                <div class="d-flex align-items-center mb-2">
                    <div class="analytics-icon bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> me-3">
                        <i class="<?= $icon ?>"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0 text-<?= $color ?>"><?= esc($title) ?></h6>
                        <?php if ($subtitle): ?>
                        <small class="text-muted"><?= esc($subtitle) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Main Value -->
                <div class="analytics-value mb-2">
                    <h2 class="mb-0 fw-bold text-<?= $color ?>"><?= is_numeric($value) ? number_format($value) : esc($value) ?></h2>
                </div>
                
                <!-- Trend Information -->
                <?php if (!empty($trend)): ?>
                <div class="analytics-trend">
                    <span class="<?= $trendClass ?> fw-medium">
                        <i class="<?= $trendIcon ?> me-1"></i>
                        <?= esc($trendValue) ?>
                    </span>
                    <?php if ($trendPeriod): ?>
                    <span class="text-muted ms-2"><?= esc($trendPeriod) ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sparkline Chart -->
            <?php if (!empty($sparkline)): ?>
            <div class="analytics-sparkline">
                <canvas id="<?= $sparklineId ?>" width="80" height="40"></canvas>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Progress Bar (optional) -->
    <?php if (isset($trend['progress'])): ?>
    <div class="card-footer p-0">
        <div class="progress" style="height: 4px; border-radius: 0;">
            <div class="progress-bar bg-<?= $color ?>" 
                 role="progressbar" 
                 style="width: <?= $trend['progress'] ?>%"
                 aria-valuenow="<?= $trend['progress'] ?>" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if ($url): ?>
</a>
<?php endif; ?>

<!-- Sparkline Chart Script -->
<?php if (!empty($sparkline)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('<?= $sparklineId ?>');
    if (!ctx) return;
    
    const sparklineData = <?= json_encode(array_values($sparkline)) ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: Array.from({length: sparklineData.length}, (_, i) => i + 1),
            datasets: [{
                data: sparklineData,
                borderColor: 'var(--<?= $color ?>-color, #007bff)',
                backgroundColor: 'transparent',
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 0,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            },
            scales: {
                x: { display: false },
                y: { display: false }
            },
            elements: {
                point: { radius: 0 }
            },
            interaction: {
                intersect: false
            }
        }
    });
});
</script>
<?php endif; ?>