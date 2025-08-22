<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="<?= $meta_description ?? 'Logistics Management System' ?>">
    <meta name="author" content="<?= COMPANY_NAME ?>">
    <title><?= $title ?? 'Dashboard' ?> - <?= APP_NAME ?></title>
    
    <!-- Mobile Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#1a2332">
    <meta name="msapplication-navbutton-color" content="#1a2332">
    <meta name="apple-mobile-web-app-title" content="<?= APP_NAME ?>">
    
    <!-- Favicon and Touch Icons -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/logo.jpg') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/images/puninar_logo.webp') ?>">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6.x -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
    
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
    
    <!-- Additional CSS -->
    <?= $this->renderSection('css') ?>
</head>
<body class="app-body">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay d-none">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="loading-text mt-2">Loading...</div>
        </div>
    </div>

    <!-- App Wrapper -->
    <div class="app-wrapper">
        <!-- Navigation Header -->
        <?= $this->include('layouts/partials/navbar') ?>
        
        <!-- App Content -->
        <div class="app-content">
            <!-- Sidebar -->
            <?= $this->include('layouts/partials/sidebar') ?>
            
            <!-- Main Content -->
            <main class="main-content">
                <!-- Breadcrumb -->
                <?= $this->include('layouts/partials/breadcrumb') ?>
                
                <!-- Page Content -->
                <div class="content-wrapper">
                    <!-- Flash Messages -->
                    <?= $this->include('layouts/partials/flash_messages') ?>
                    
                    <!-- Main Content Area -->
                    <?= $this->renderSection('content') ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer">
        <!-- Toasts will be dynamically added here -->
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Progressive Enhancement -->
    <script src="<?= base_url('assets/js/progressive-enhancement.js') ?>"></script>
    
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <script src="<?= base_url('assets/js/datatable.js') ?>"></script>
    <script src="<?= base_url('assets/js/form-validator.js') ?>"></script>
    
    <!-- Additional JS -->
    <?= $this->renderSection('js') ?>
</body>
</html>