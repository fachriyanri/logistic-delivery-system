<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\UserService;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Display list of users (excluding admin)
     */
    public function index(): string
    {
        // Check if current user is admin
        if (!$this->isAdmin()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }

        // Get users data
        $users = $this->userService->getNonAdminUsers();
        $statistics = $this->userService->getUserStatistics();

        $data = [
            'title' => 'User Management',
            'users' => $users,
            'statistics' => $statistics
        ];

        return view('users/index', $data);
    }

    /**
     * Show form for editing user
     */
    public function edit(?string $id = null): string
    {
        // Check if current user is admin
        if (!$this->isAdmin()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
        }

        if (empty($id)) {
            session()->setFlashdata('error', 'User ID is required');
            return redirect()->to('/users');
        }

        $user = $this->userService->getUserById($id);
        if (!$user) {
            session()->setFlashdata('error', 'User not found');
            return redirect()->to('/users');
        }

        // Prevent editing admin account
        if ($user->level === USER_LEVEL_ADMIN) {
            session()->setFlashdata('error', 'Cannot edit administrator account');
            return redirect()->to('/users');
        }

        $data = [
            'title' => 'Edit User',
            'user' => $user
        ];

        return view('users/edit', $data);
    }

    /**
     * Update user status
     */
    public function update(): ResponseInterface
    {
        // Check if current user is admin
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $post = $this->request->getPost();
        
        if (!$post) {
            return redirect()->to('/users');
        }

        $id = $post['id_user'] ?? '';
        
        if (empty($id)) {
            session()->setFlashdata('error', 'User ID is required');
            return redirect()->to('/users');
        }

        // Prepare data
        $data = [
            'is_active' => $post['is_active'] ?? 0
        ];

        // Update user
        $result = $this->userService->updateUserStatus($id, $data);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/users');
    }

    /**
     * Delete user
     */
    public function delete(?string $id = null): ResponseInterface
    {
        // Check if current user is admin
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        if (empty($id)) {
            session()->setFlashdata('error', 'User ID is required');
            return redirect()->to('/users');
        }

        $result = $this->userService->deleteUser($id);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/users');
    }

    /**
     * AJAX endpoint to toggle user status
     */
    public function toggleStatus(): ResponseInterface
    {
        // Check if current user is admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        $id = $this->request->getPost('user_id');
        
        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID is required'
            ]);
        }

        $result = $this->userService->toggleUserStatus($id);
        
        return $this->response->setJSON($result);
    }

    /**
     * Check if current user is admin
     */
    private function isAdmin(): bool
    {
        return session()->get('level') === USER_LEVEL_ADMIN;
    }
}