<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><?= $title ?></h3>
                    <div class="card-tools">
                        <div class="row">
                            <div class="col-auto">
                                <small class="text-muted">
                                    Total: <?= $statistics['non_admin_users'] ?> users |
                                    Active: <?= $statistics['active_users'] ?> |
                                    Inactive: <?= $statistics['inactive_users'] ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>User ID</th>
                                    <th>Username</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= esc($user->id_user) ?></td>
                                        <td><?= esc($user->username) ?></td>
                                        <td><?= esc($user->getLevelName()) ?></td>
                                        <td>
                                            <span class="badge <?= $user->getStatusBadgeClass() ?>">
                                                <?= $user->getStatusDisplay() ?>
                                            </span>
                                        </td>
                                        <td><?= $user->created_at ? $user->created_at->format('Y-m-d H:i') : '-' ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('users/edit/' . $user->id_user) ?>" 
                                                   class="btn btn-sm btn-warning" title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('users/delete/' . $user->id_user) ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" 
                                                   title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No users found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // User management functionality can be added here if needed
    console.log('User management page loaded');
});
</script>
<?= $this->endSection() ?>