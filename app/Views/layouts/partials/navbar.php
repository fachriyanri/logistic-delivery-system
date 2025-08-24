<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center" href="<?= base_url('/dashboard') ?>">
            <img src="<?= base_url('assets/images/puninar_logo.webp') ?>" 
                 alt="<?= COMPANY_NAME ?>" 
                 height="40" 
                 class="me-2">
            <div class="brand-text d-none d-md-block">
                <div class="brand-name"><?= APP_NAME ?></div>
                <div class="brand-subtitle"><?= COMPANY_NAME ?></div>
            </div>
        </a>

        <!-- Mobile Menu Toggle -->
        <button class="navbar-toggler d-lg-none" 
                type="button" 
                data-bs-toggle="offcanvas" 
                data-bs-target="#mobileSidebar"
                aria-controls="mobileSidebar"
                aria-expanded="false" 
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Desktop Navigation -->
        <div class="navbar-nav ms-auto d-none d-lg-flex">
            <!-- Notifications -->
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle position-relative" 
                   href="#" 
                   id="notificationDropdown" 
                   role="button" 
                   data-bs-toggle="dropdown" 
                   aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge d-none">
                        0
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                    <li class="dropdown-header">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="dropdown-item-text text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        No new notifications
                    </li>
                </ul>
            </div>

            <!-- User Menu -->
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" 
                   href="#" 
                   id="userDropdown" 
                   role="button" 
                   data-bs-toggle="dropdown" 
                   aria-expanded="false">
                    <div class="user-avatar me-2">
                        <i class="fas fa-user-circle fa-lg"></i>
                    </div>
                    <div class="user-info d-none d-xl-block">
                        <div class="user-name"><?= session('username') ?? '' ?></div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userDropdown">
                    <li class="dropdown-header">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-circle fa-2x me-3"></i>
                            <div>
                                <div class="fw-bold"><?= session('username') ?? '' ?></div>
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('/profile') ?>">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('/change-password') ?>">
                            <i class="fas fa-key me-2"></i>Change Password
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" onclick="confirmLogout()">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">
                    <i class="fas fa-sign-out-alt me-2"></i>Confirm Logout
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to logout from the system?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <a href="<?= base_url('/logout') ?>" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmLogout() {
    const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
    logoutModal.show();
}
</script>