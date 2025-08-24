<?php

namespace App\Controllers;

use App\Models\UserModel;

class PasswordController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Ubah Password',
            'active_menu' => 'settings'
        ];

        return view('auth/change_password', $data);
    }

    public function update()
    {
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        $messages = [
            'current_password' => [
                'required' => 'Password saat ini harus diisi'
            ],
            'new_password' => [
                'required' => 'Password baru harus diisi',
                'min_length' => 'Password baru minimal 6 karakter'
            ],
            'confirm_password' => [
                'required' => 'Konfirmasi password harus diisi',
                'matches' => 'Konfirmasi password tidak sama dengan password baru'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Fix: Use 'id_user' instead of 'user_id' to match session key set in AuthController
        $userId = session()->get('id_user');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        // Get current user
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }

        // Verify current password
        // Access password as object property, not array, to match how AuthController works
        if (!password_verify($currentPassword, $user->password)) {
            return redirect()->back()->with('error', 'Password saat ini salah');
        }

        // Update password using the dedicated method to ensure proper hashing
        if ($this->userModel->updatePassword($userId, $newPassword)) {
            return redirect()->back()->with('success', 'Password berhasil diubah');
        } else {
            return redirect()->back()->with('error', 'Gagal mengubah password');
        }
    }
}