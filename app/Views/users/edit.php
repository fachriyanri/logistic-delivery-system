<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $title ?></h3>
                    <div class="card-tools">
                        <a href="<?= base_url('users') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
                
                <form action="<?= base_url('users/update') ?>" method="post">
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

                        <!-- Hidden Fields -->
                        <input type="hidden" name="id_user" value="<?= $user->id_user ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <!-- User ID (readonly) -->
                                <div class="mb-3">
                                    <label for="user_id_display" class="form-label">User ID</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="user_id_display" 
                                           value="<?= esc($user->id_user) ?>"
                                           readonly>
                                </div>

                                <!-- Username (readonly) -->
                                <div class="mb-3">
                                    <label for="username_display" class="form-label">Username</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="username_display" 
                                           value="<?= esc($user->username) ?>"
                                           readonly>
                                </div>

                                <!-- User Level (readonly) -->
                                <div class="mb-3">
                                    <label for="level_display" class="form-label">User Level</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="level_display" 
                                           value="<?= esc($user->getLevelName()) ?>"
                                           readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Status -->
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">User Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="is_active" name="is_active" required>
                                        <option value="1" <?= $user->isActive() ? 'selected' : '' ?>>Active</option>
                                        <option value="0" <?= !$user->isActive() ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                    <div class="form-text">
                                        Active users can log in to the system. Inactive users will be denied access.
                                    </div>
                                </div>

                                <!-- Current Status Display -->
                                <div class="mb-3">
                                    <label class="form-label">Current Status</label>
                                    <div>
                                        <span class="<?= $user->getStatusBadgeClass() ?> badge-lg">
                                            <?= $user->getStatusDisplay() ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Created At (readonly) -->
                                <div class="mb-3">
                                    <label for="created_at_display" class="form-label">Created At</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="created_at_display" 
                                           value="<?= $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '-' ?>"
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Warning Message -->
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Important:</strong> Changing a user's status to "Inactive" will prevent them from logging into the system. 
                            Make sure this is intended before saving the changes.
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                            <a href="<?= base_url('users') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add confirmation when changing status to inactive
    const statusSelect = document.getElementById('is_active');
    const originalValue = statusSelect.value;
    
    statusSelect.addEventListener('change', function() {
        if (this.value === '0' && originalValue === '1') {
            if (!confirm('Are you sure you want to deactivate this user? They will not be able to log in until reactivated.')) {
                this.value = originalValue;
            }
        }
    });
    
    // Add form submission confirmation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const newStatus = statusSelect.value;
        const statusText = newStatus === '1' ? 'active' : 'inactive';
        
        if (!confirm(`Are you sure you want to set this user's status to ${statusText}?`)) {
            e.preventDefault();
        }
    });
});
</script>

<style>
.badge-lg {
    font-size: 0.9em;
    padding: 0.5em 0.75em;
}
</style>
<?= $this->endSection() ?>