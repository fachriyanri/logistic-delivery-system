<?php

namespace App\Controllers;

use App\Models\UserModel;

class ProfileController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Fix: Use 'id_user' instead of 'user_id' to match session key set in BaseController
        $userId = session()->get('id_user');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User tidak ditemukan');
        }

        // Convert to array if it's an object
        if (is_object($user)) {
            $user = $user->toArray();
        }

        $data = [
            'title' => 'Profil User',
            'user' => $user,
            'active_menu' => 'profile'
        ];

        return view('auth/profile', $data);
    }

    public function update()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]'
        ];

        $messages = [
            'username' => [
                'required' => 'Username harus diisi',
                'min_length' => 'Username minimal 3 karakter',
                'max_length' => 'Username maksimal 50 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Fix: Use 'id_user' instead of 'user_id' to match session key set in BaseController
        $userId = session()->get('id_user');
        $username = $this->request->getPost('username');

        // Check if username already exists (except current user)
        $existingUser = $this->userModel->where('username', $username)
                                        ->where('id_user !=', $userId)
                                        ->first();

        if ($existingUser) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan');
        }

        $updateData = [
            'id_user' => $userId,
            'username' => $username,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->userModel->save($updateData)) {
            // Update session username
            session()->set('username', $username);
            return redirect()->back()->with('success', 'Profil berhasil diperbarui');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui profil');
        }
    }
}