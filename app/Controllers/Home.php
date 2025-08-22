<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Redirect to login if not authenticated
        if (!$this->isAuthenticated()) {
            return redirect()->to('/login');
        }

        // Redirect to dashboard if authenticated
        return redirect()->to('/dashboard');
    }
}