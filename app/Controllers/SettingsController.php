<?php

namespace App\Controllers;

class SettingsController extends BaseController
{
    public function index()
    {
        // Only admin can access settings
        if (session()->get('level') != 1) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $data = [
            'title' => 'Pengaturan Sistem',
            'active_menu' => 'settings'
        ];

        return view('settings/index', $data);
    }

    public function update()
    {
        // Only admin can update settings
        if (session()->get('level') != 1) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses fitur ini.');
        }

        // Here you can implement various system settings updates
        // For now, just redirect back with success message
        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui');
    }
}