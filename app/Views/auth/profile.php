<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $title ?></h3>
                </div>
                
                <form action="<?= base_url('profile') ?>" method="post">
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

                        <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <!-- User ID -->
                        <div class="mb-3">
                            <label for="id_user" class="form-label">ID User</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="id_user" 
                                   value="<?= esc($user['id_user']) ?>"
                                   readonly>
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   value="<?= old('username', $user['username']) ?>"
                                   maxlength="50"
                                   required>
                            <div class="form-text">Minimal 3 karakter, maksimal 50 karakter</div>
                        </div>

                        <!-- Level -->
                        <div class="mb-3">
                            <label for="level" class="form-label">Level</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="level" 
                                   value="<?php
                                       switch($user['level']) {
                                           case 1: echo 'Admin'; break;
                                           case 2: echo 'Finance'; break;
                                           case 3: echo 'Gudang'; break;
                                           default: echo 'Unknown';
                                       }
                                   ?>"
                                   readonly>
                        </div>

                        <!-- Created At -->
                        <div class="mb-3">
                            <label for="created_at" class="form-label">Dibuat Pada</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="created_at" 
                                   value="<?= date('d/m/Y H:i:s', strtotime($user['created_at'])) ?>"
                                   readonly>
                        </div>

                        <!-- Updated At -->
                        <?php if (!empty($user['updated_at'])): ?>
                        <div class="mb-3">
                            <label for="updated_at" class="form-label">Terakhir Diperbarui</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="updated_at" 
                                   value="<?= date('d/m/Y H:i:s', strtotime($user['updated_at'])) ?>"
                                   readonly>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Perbarui Profil
                            </button>
                            <div>
                                <a href="<?= base_url('change-password') ?>" class="btn btn-warning">
                                    <i class="fas fa-key"></i> Ubah Password
                                </a>
                                <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>