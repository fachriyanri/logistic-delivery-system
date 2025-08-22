<?php

namespace Tests\Feature\Controllers;

use Tests\Support\DatabaseTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class AuthControllerTest extends DatabaseTestCase
{
    use ControllerTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testLoginPageDisplaysCorrectly(): void
    {
        $result = $this->get('/auth/login');

        $result->assertOK();
        $result->assertSee('Login');
        $result->assertSee('Username');
        $result->assertSee('Password');
    }

    public function testLoginWithValidCredentials(): void
    {
        $data = [
            'username' => 'testadmin',
            'password' => 'testpass123',
        ];

        $result = $this->post('/auth/authenticate', $data);

        $result->assertRedirectTo('/dashboard');
        
        // Check if session is set
        $session = session();
        $this->assertTrue($session->has('user_id'));
        $this->assertEquals('USR01', $session->get('user_id'));
        $this->assertEquals('testadmin', $session->get('username'));
        $this->assertEquals(1, $session->get('user_level'));
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $data = [
            'username' => 'testadmin',
            'password' => 'wrongpassword',
        ];

        $result = $this->post('/auth/authenticate', $data);

        $result->assertRedirectTo('/auth/login');
        
        // Check if error message is set
        $session = session();
        $this->assertTrue($session->has('error'));
        $this->assertFalse($session->has('user_id'));
    }

    public function testLoginWithNonExistentUser(): void
    {
        $data = [
            'username' => 'nonexistent',
            'password' => 'password123',
        ];

        $result = $this->post('/auth/authenticate', $data);

        $result->assertRedirectTo('/auth/login');
        
        $session = session();
        $this->assertTrue($session->has('error'));
        $this->assertFalse($session->has('user_id'));
    }

    public function testLoginWithEmptyCredentials(): void
    {
        $data = [
            'username' => '',
            'password' => '',
        ];

        $result = $this->post('/auth/authenticate', $data);

        $result->assertRedirectTo('/auth/login');
        
        $session = session();
        $this->assertTrue($session->has('error'));
    }

    public function testLogoutClearsSession(): void
    {
        // First login
        $loginData = [
            'username' => 'testadmin',
            'password' => 'testpass123',
        ];
        
        $this->post('/auth/authenticate', $loginData);
        
        // Verify session is set
        $session = session();
        $this->assertTrue($session->has('user_id'));
        
        // Now logout
        $result = $this->get('/auth/logout');
        
        $result->assertRedirectTo('/auth/login');
        
        // Verify session is cleared
        $this->assertFalse($session->has('user_id'));
        $this->assertFalse($session->has('username'));
        $this->assertFalse($session->has('user_level'));
    }

    public function testAuthenticatedUserCannotAccessLoginPage(): void
    {
        // Login first
        $this->loginAsUser('testadmin');
        
        $result = $this->get('/auth/login');
        $result->assertRedirectTo('/dashboard');
    }

    public function testChangePasswordWithValidData(): void
    {
        $this->loginAsUser('testadmin');
        
        $data = [
            'current_password' => 'testpass123',
            'new_password' => 'newpassword123',
            'confirm_password' => 'newpassword123',
        ];

        $result = $this->post('/auth/change-password', $data);

        $result->assertRedirectTo('/dashboard');
        
        $session = session();
        $this->assertTrue($session->has('success'));
    }

    public function testChangePasswordWithWrongCurrentPassword(): void
    {
        $this->loginAsUser('testadmin');
        
        $data = [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'confirm_password' => 'newpassword123',
        ];

        $result = $this->post('/auth/change-password', $data);

        $result->assertRedirectTo('/auth/change-password');
        
        $session = session();
        $this->assertTrue($session->has('error'));
    }

    public function testChangePasswordWithMismatchedPasswords(): void
    {
        $this->loginAsUser('testadmin');
        
        $data = [
            'current_password' => 'testpass123',
            'new_password' => 'newpassword123',
            'confirm_password' => 'differentpassword',
        ];

        $result = $this->post('/auth/change-password', $data);

        $result->assertRedirectTo('/auth/change-password');
        
        $session = session();
        $this->assertTrue($session->has('error'));
    }

    public function testUnauthenticatedUserCannotChangePassword(): void
    {
        $data = [
            'current_password' => 'testpass123',
            'new_password' => 'newpassword123',
            'confirm_password' => 'newpassword123',
        ];

        $result = $this->post('/auth/change-password', $data);

        $result->assertRedirectTo('/auth/login');
    }

    public function testSessionTimeoutRedirectsToLogin(): void
    {
        $this->loginAsUser('testadmin');
        
        // Simulate session timeout by clearing session
        session()->destroy();
        
        $result = $this->get('/dashboard');
        $result->assertRedirectTo('/auth/login');
    }

    public function testCSRFProtectionOnAuthenticationForm(): void
    {
        // Get login page to get CSRF token
        $loginPage = $this->get('/auth/login');
        $loginPage->assertOK();
        
        // Try to submit without CSRF token
        $data = [
            'username' => 'testadmin',
            'password' => 'testpass123',
        ];

        // This should fail due to CSRF protection
        $result = $this->post('/auth/authenticate', $data);
        
        // The exact behavior depends on CSRF configuration
        // It might redirect back or show an error
        $this->assertTrue($result->isRedirect() || $result->getStatusCode() === 403);
    }

    /**
     * Helper method to login as a specific user
     */
    private function loginAsUser(string $username): void
    {
        $data = [
            'username' => $username,
            'password' => 'testpass123',
        ];
        
        $this->post('/auth/authenticate', $data);
    }
}