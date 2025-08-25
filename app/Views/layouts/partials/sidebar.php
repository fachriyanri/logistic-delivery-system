<?php
$userLevel = session('level');
$currentUri = uri_string();
?>

<!-- Desktop Sidebar -->
<aside class="sidebar d-none d-lg-block">
    <div class="sidebar-content">
        <!-- Navigation Menu -->
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link <?= ($currentUri === 'dashboard' || $currentUri === '') ? 'active' : '' ?>" 
                       href="<?= base_url('/dashboard') ?>">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <!-- Master Data Section (Admin & Gudang) -->
                <?php if ($userLevel == 1 || $userLevel == 3): ?>
                <li class="nav-section">
                    <span class="nav-section-title">Master Data</span>
                </li>

                <!-- Categories (Admin & Gudang) -->
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($currentUri, 'kategori') !== false) ? 'active' : '' ?>" 
                       href="<?= base_url('/kategori') ?>">
                        <i class="fas fa-tags nav-icon"></i>
                        <span class="nav-text">Categories</span>
                    </a>
                </li>

                <!-- Items (Admin & Gudang) -->
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($currentUri, 'barang') !== false) ? 'active' : '' ?>" 
                       href="<?= base_url('/barang') ?>">
                        <i class="fas fa-boxes nav-icon"></i>
                        <span class="nav-text">Items</span>
                    </a>
                </li>

                <!-- Couriers (Admin & Gudang) -->
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($currentUri, 'kurir') !== false) ? 'active' : '' ?>" 
                       href="<?= base_url('/kurir') ?>">
                        <i class="fas fa-motorcycle nav-icon"></i>
                        <span class="nav-text">Couriers</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Customer Management (Admin & Finance) -->
                <?php if ($userLevel == 1 || $userLevel == 2): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($currentUri, 'pelanggan') !== false) ? 'active' : '' ?>" 
                       href="<?= base_url('/pelanggan') ?>">
                        <i class="fas fa-users nav-icon"></i>
                        <span class="nav-text">Customers</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Shipping Section -->
                <li class="nav-section">
                    <span class="nav-section-title">Shipping</span>
                </li>

                <!-- Shipments (All users) -->
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($currentUri, 'pengiriman') !== false) ? 'active' : '' ?>" 
                       href="<?= base_url('/pengiriman') ?>">
                        <i class="fas fa-shipping-fast nav-icon"></i>
                        <span class="nav-text">Shipments</span>
                    </a>
                </li>

                <!-- Administration Section (Admin only) -->
                <?php if ($userLevel == 1): ?>
                <li class="nav-section">
                    <span class="nav-section-title">Administration</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (strpos($currentUri, 'users') !== false) ? 'active' : '' ?>" 
                       href="<?= base_url('/users') ?>">
                        <i class="fas fa-user-cog nav-icon"></i>
                        <span class="nav-text">User Management</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>

<!-- Mobile Sidebar (Offcanvas) -->
<div class="offcanvas offcanvas-start d-lg-none" 
     tabindex="-1" 
     id="mobileSidebar" 
     aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileSidebarLabel">
            <img src="<?= base_url('assets/images/puninar_logo.webp') ?>" 
                 alt="<?= COMPANY_NAME ?>" 
                 height="30" 
                 class="me-2">
            Menu
        </h5>
        <button type="button" 
                class="btn-close" 
                data-bs-dismiss="offcanvas" 
                aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Mobile Navigation Menu -->
        <nav class="mobile-nav">
            <ul class="nav flex-column">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link <?= ($currentUri === 'dashboard' || $currentUri === '') ? 'active' : '' ?>" 
                       href="<?= base_url('/dashboard') ?>"
                       data-bs-dismiss="offcanvas">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <!-- Master Data Section (Admin & Gudang) -->
                <?php if ($userLevel == 1 || $userLevel == 3): ?>
                <li class="nav-section">
                    <span class="nav-section-title">Master Data</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (strpos($currentUri, 'kategori') !== false) ? 'active' : '' ?>" 
                       href="<?= base_url('/kategori') ?>"
                       data-bs-dismiss="offcanvas">
                        <i class="fas fa-tags nav-icon"></i>
                        <span class="nav-text">Categories</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (strpos($currentUri, 'barang') !== false) ? 'active' : '' ?>" 
                       href="<?= base_url('/barang') ?>"
                       data-bs-dismiss="offcanvas">
                        <i class="fas fa-boxes nav-icon"></i>
                        <span class="nav-text">Items</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (strpos($currentUri, 'kurir') !== false) ? 'active' : '' ?>" 
                       href="<?= base_url('/kurir') ?>"
                       data-bs-dismiss="offcanvas">
                        <i class="fas fa-motorcycle nav-icon"></i>
                        <span class="nav-text">Couriers</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Customer Management (Admin & Finance) -->
                <?php if ($userLevel == 1 || $userLevel == 2): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($currentUri, 'pelanggan') !== false) ? 'active' : '' ?>" 
                       href="<?= base_url('/pelanggan') ?>"
                       data-bs-dismiss="offcanvas">
                        <i class="fas fa-users nav-icon"></i>
                        <span class="nav-text">Customers</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Shipping Section -->
                <li class="nav-section">
                    <span class="nav-section-title">Shipping</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (strpos($currentUri, 'pengiriman') !== false) ? 'active' : '' ?>" 
                       href="<?= base_url('/pengiriman') ?>"
                       data-bs-dismiss="offcanvas">
                        <i class="fas fa-shipping-fast nav-icon"></i>
                        <span class="nav-text">Shipments</span>
                    </a>
                </li>

                <!-- Administration Section (Admin only) -->
                <?php if ($userLevel == 1): ?>
                <li class="nav-section">
                    <span class="nav-section-title">Administration</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (strpos($currentUri, 'users') !== false) ? 'active' : '' ?>" 
                       href="<?= base_url('/users') ?>"
                       data-bs-dismiss="offcanvas">
                        <i class="fas fa-user-cog nav-icon"></i>
                        <span class="nav-text">User Management</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Mobile User Actions -->
                <li class="nav-section">
                    <span class="nav-section-title">Account</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" 
                       href="<?= base_url('/profile') ?>"
                       data-bs-dismiss="offcanvas">
                        <i class="fas fa-user nav-icon"></i>
                        <span class="nav-text">Profile</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" 
                       href="<?= base_url('/change-password') ?>"
                       data-bs-dismiss="offcanvas">
                        <i class="fas fa-key nav-icon"></i>
                        <span class="nav-text">Change Password</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-danger" 
                       href="#" 
                       onclick="confirmLogout()"
                       data-bs-dismiss="offcanvas">
                        <i class="fas fa-sign-out-alt nav-icon"></i>
                        <span class="nav-text">Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>