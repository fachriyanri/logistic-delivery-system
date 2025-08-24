<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Authentication Controller
 * 
 * Handles user authentication operations including login, logout, and session management.
 * Provides secure authentication with CSRF protection, input validation, and activity logging.
 * 
 * @package App\Controllers
 * @author  CodeIgniter Logistics System
 * @version 1.0.0
 * @since   2024-01-01
 */
class AuthController extends BaseController
{
    /**
     * User model instance for database operations
     * 
     * @var UserModel
     */
    protected UserModel $userModel;

    /**
     * Constructor - Initialize dependencies
     * 
     * Sets up the UserModel instance for authentication operations.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display login form
     * 
     * Shows the login page for user authentication. If user is already authenticated,
     * redirects to the dashboard. Includes CSRF token and validation services.
     * 
     * @return string The rendered login view or redirect response
     * 
     * @example
     * // Access login page
     * GET /login
     * 
     * @see view('auth/login') Login view template
     * @see isAuthenticated() Check if user is already logged in
     */
    public function index(): string
    {
        // Redirect to dashboard if already logged in
        if ($this->isAuthenticated()) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login - ' . APP_NAME,
            'validation' => \Config\Services::validation()
        ];

        return view('auth/login', $data);
    }

    /**
     * Process login authentication
     * 
     * Validates user credentials and creates authenticated session. Includes comprehensive
     * input validation, CSRF protection, password verification, and activity logging.
     * Supports both AJAX and regular form submissions.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface|string JSON response for AJAX or redirect for regular requests
     * 
     * @throws \Exception When database operations fail
     * 
     * @example
     * // AJAX login request
     * POST /auth/authenticate
     * Content-Type: application/json
     * {
     *     "username": "adminpuninar",
     *     "password": "AdminPuninar123"
     * }
     * 
     * @example
     * // Regular form submission
     * POST /auth/authenticate
     * Content-Type: application/x-www-form-urlencoded
     * username=adminpuninar&password=AdminPuninar123
     * 
     * @see validateCSRF() CSRF token validation
     * @see logActivity() Activity logging
     * @see password_verify() Password verification
     */
    public function authenticate()
    {
        // Validate CSRF token
        if (!$this->validateCSRF()) {
            return $this->jsonError('Invalid CSRF token', [], 403);
        }

        // Validation rules
        $rules = [
            'username' => [
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'Username is required',
                    'min_length' => 'Username must be at least 3 characters',
                    'max_length' => 'Username cannot exceed 50 characters'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password is required',
                    'min_length' => 'Password must be at least 6 characters'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->jsonError('Validation failed', $this->validator->getErrors());
            }
            
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Attempt to authenticate user
        $user = $this->userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user->password)) {
            // Set session data
            $sessionData = [
                'isLoggedIn' => true,
                'id_user' => $user->id_user,
                'username' => $user->username,
                'level' => $user->level
            ];

            $this->session->set($sessionData);

            // Log successful login
            $this->logActivity('login', [
                'user_id' => $user->id_user,
                'username' => $user->username
            ]);

            // Get redirect URL or default to dashboard
            $redirectUrl = $this->session->get('redirect_url') ?? '/dashboard';
            $this->session->remove('redirect_url');

            if ($this->request->isAJAX()) {
                return $this->jsonSuccess('Login successful', ['redirect' => $redirectUrl]);
            }

            return redirect()->to($redirectUrl)->with('success', 'Welcome back, ' . $user->username . '!');
        }

        // Authentication failed
        $this->logActivity('login_failed', [
            'username' => $username,
            'ip_address' => $this->request->getIPAddress()
        ]);

        if ($this->request->isAJAX()) {
            return $this->jsonError('Invalid Username And Password');
        }

        return redirect()->back()->withInput()->with('error', 'Invalid Username And Password');
    }

    /**
     * Logout user
     * 
     * Destroys user session and logs logout activity. Supports both AJAX and regular
     * requests with appropriate responses. Ensures complete session cleanup.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response for AJAX or redirect for regular requests
     * 
     * @example
     * // AJAX logout request
     * POST /auth/logout
     * 
     * @example
     * // Regular logout request
     * GET /auth/logout
     * 
     * @see logActivity() Activity logging
     * @see session->destroy() Session cleanup
     */
    public function logout()
    {
        // Log logout activity
        if ($this->isAuthenticated()) {
            $this->logActivity('logout', [
                'user_id'  => session()->get('id_user'),
                'username' => session()->get('username')
            ]);
        }

        // Destroy session
        $this->session->destroy();

        if ($this->request->isAJAX()) {
            return $this->jsonSuccess('Logged out successfully', ['redirect' => '/login']);
        }

        return redirect()->to('/login')->with('success', 'You have been logged out successfully');
    }
}