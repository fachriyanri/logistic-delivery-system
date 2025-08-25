<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use App\Models\UserModel;
use App\Entities\UserEntity;

class UserService
{
    protected UserModel $userModel;
    protected BaseConnection $db;

    public function __construct()
    {
        $this->userModel = model('UserModel');
        $this->db = \Config\Database::connect();
    }

    /**
     * Get all non-admin users for management
     */
    public function getNonAdminUsers(): array
    {
        return $this->userModel->getNonAdminUsers();
    }

    /**
     * Get user by ID
     */
    public function getUserById(string $id): ?UserEntity
    {
        return $this->userModel->find($id);
    }

    /**
     * Update user status
     */
    public function updateUserStatus(string $id, array $data): array
    {
        $result = ['success' => false, 'message' => '', 'data' => null];

        try {
            // Check if user exists
            $existingUser = $this->userModel->find($id);
            if (!$existingUser) {
                $result['message'] = 'User not found';
                return $result;
            }

            // Prevent admin from being modified
            if ($existingUser->level === USER_LEVEL_ADMIN) {
                $result['message'] = 'Cannot modify administrator account';
                return $result;
            }

            // Use CodeIgniter's validation library
            $validation = \Config\Services::validation();

            // Define the validation rules
            $validation->setRules([
                'is_active' => 'required|in_list[0,1]'
            ]);

            if (!$validation->run($data)) {
                $result['message'] = implode(', ', $validation->getErrors());
                return $result;
            }

            // Update user
            if ($this->userModel->update($id, $data)) {
                $result['success'] = true;
                $result['message'] = 'User status updated successfully';
                $result['data'] = $this->getUserById($id);
            } else {
                $result['message'] = 'Failed to update user: ' . implode(', ', $this->userModel->errors());
            }
        } catch (\Exception $e) {
            $result['message'] = 'An error occurred: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Delete user
     */
    public function deleteUser(string $id): array
    {
        $result = ['success' => false, 'message' => ''];

        try {
            // Check if user exists
            $user = $this->userModel->find($id);
            if (!$user) {
                $result['message'] = 'User not found';
                return $result;
            }

            // Prevent admin from being deleted
            if ($user->level === USER_LEVEL_ADMIN) {
                $result['message'] = 'Cannot delete administrator account';
                return $result;
            }

            // Check if user can be deleted
            if (!$this->userModel->canBeDeleted($id)) {
                $result['message'] = 'User cannot be deleted';
                return $result;
            }

            // Delete user
            if ($this->userModel->delete($id)) {
                $result['success'] = true;
                $result['message'] = 'User deleted successfully';
            } else {
                $result['message'] = 'Failed to delete user';
            }
        } catch (\Exception $e) {
            $result['message'] = 'An error occurred: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus(string $id): array
    {
        $result = ['success' => false, 'message' => '', 'data' => null];

        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                $result['message'] = 'User not found';
                return $result;
            }

            // Prevent admin from being modified
            if ($user->level === USER_LEVEL_ADMIN) {
                $result['message'] = 'Cannot modify administrator status';
                return $result;
            }

            if ($this->userModel->toggleActiveStatus($id)) {
                $result['success'] = true;
                $result['message'] = 'User status toggled successfully';
                $result['data'] = $this->getUserById($id);
            } else {
                $result['message'] = 'Failed to toggle user status';
            }
        } catch (\Exception $e) {
            $result['message'] = 'An error occurred: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Get user statistics
     */
    public function getUserStatistics(): array
    {
        try {
            $totalUsers = $this->userModel->countAllResults();
            $activeUsers = $this->userModel->where('is_active', 1)->countAllResults();
            $inactiveUsers = $this->userModel->where('is_active', 0)->countAllResults();
            $nonAdminUsers = $this->userModel->where('level !=', USER_LEVEL_ADMIN)->countAllResults();

            return [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'inactive_users' => $inactiveUsers,
                'non_admin_users' => $nonAdminUsers
            ];
        } catch (\Exception $e) {
            return [
                'total_users' => 0,
                'active_users' => 0,
                'inactive_users' => 0,
                'non_admin_users' => 0
            ];
        }
    }
}