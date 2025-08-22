<?php
// Generate breadcrumb based on current URI
$uri = service('uri');
$segments = $uri->getSegments();
$breadcrumbs = [];

// Always start with Dashboard
$breadcrumbs[] = [
    'title' => 'Dashboard',
    'url' => base_url('/dashboard'),
    'icon' => 'fas fa-tachometer-alt'
];

// Build breadcrumb based on segments
if (!empty($segments)) {
    $currentPath = '';
    
    foreach ($segments as $index => $segment) {
        $currentPath .= '/' . $segment;
        
        // Skip numeric segments (usually IDs)
        if (is_numeric($segment)) {
            continue;
        }
        
        // Map segments to readable names
        $segmentNames = [
            'kategori' => ['title' => 'Categories', 'icon' => 'fas fa-tags'],
            'barang' => ['title' => 'Items', 'icon' => 'fas fa-boxes'],
            'kurir' => ['title' => 'Couriers', 'icon' => 'fas fa-motorcycle'],
            'pelanggan' => ['title' => 'Customers', 'icon' => 'fas fa-users'],
            'pengiriman' => ['title' => 'Shipments', 'icon' => 'fas fa-shipping-fast'],
            'laporan' => ['title' => 'Reports', 'icon' => 'fas fa-chart-bar'],
            'users' => ['title' => 'User Management', 'icon' => 'fas fa-user-cog'],
            'profile' => ['title' => 'Profile', 'icon' => 'fas fa-user'],
            'change-password' => ['title' => 'Change Password', 'icon' => 'fas fa-key'],
            'create' => ['title' => 'Create New', 'icon' => 'fas fa-plus'],
            'edit' => ['title' => 'Edit', 'icon' => 'fas fa-edit'],
            'view' => ['title' => 'View Details', 'icon' => 'fas fa-eye'],
            'manage' => ['title' => 'Manage', 'icon' => 'fas fa-cog']
        ];
        
        $segmentInfo = $segmentNames[$segment] ?? ['title' => ucfirst($segment), 'icon' => 'fas fa-folder'];
        
        // Determine if this is the last segment (current page)
        $isLast = ($index === count($segments) - 1);
        
        $breadcrumbs[] = [
            'title' => $segmentInfo['title'],
            'url' => $isLast ? null : base_url($currentPath),
            'icon' => $segmentInfo['icon'],
            'active' => $isLast
        ];
    }
}

// If we're on dashboard, mark it as active
if (empty($segments) || (count($segments) === 1 && $segments[0] === 'dashboard')) {
    $breadcrumbs[0]['active'] = true;
    $breadcrumbs[0]['url'] = null;
}
?>

<?php if (!empty($breadcrumbs)): ?>
<div class="breadcrumb-wrapper">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <!-- Page Title -->
                <div class="page-header">
                    <h1 class="page-title">
                        <?php 
                        $activeBreadcrumb = end($breadcrumbs);
                        if ($activeBreadcrumb): 
                        ?>
                            <i class="<?= $activeBreadcrumb['icon'] ?> me-2"></i>
                            <?= $activeBreadcrumb['title'] ?>
                        <?php endif; ?>
                    </h1>
                    
                    <!-- Breadcrumb Navigation -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                                <li class="breadcrumb-item <?= isset($breadcrumb['active']) && $breadcrumb['active'] ? 'active' : '' ?>">
                                    <?php if (isset($breadcrumb['url']) && $breadcrumb['url']): ?>
                                        <a href="<?= $breadcrumb['url'] ?>" class="breadcrumb-link">
                                            <i class="<?= $breadcrumb['icon'] ?> me-1"></i>
                                            <?= $breadcrumb['title'] ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="breadcrumb-current">
                                            <i class="<?= $breadcrumb['icon'] ?> me-1"></i>
                                            <?= $breadcrumb['title'] ?>
                                        </span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                </div>
            </div>
            
            <!-- Page Actions (if provided) -->
            <?php if (isset($pageActions) && !empty($pageActions)): ?>
            <div class="col-auto">
                <div class="page-actions">
                    <?php foreach ($pageActions as $action): ?>
                        <a href="<?= $action['url'] ?>" 
                           class="btn <?= $action['class'] ?? 'btn-primary' ?> <?= $action['size'] ?? '' ?>">
                            <?php if (isset($action['icon'])): ?>
                                <i class="<?= $action['icon'] ?> me-2"></i>
                            <?php endif; ?>
                            <?= $action['title'] ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>