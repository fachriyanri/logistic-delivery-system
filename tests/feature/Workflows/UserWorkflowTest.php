<?php

namespace Tests\Feature\Workflows;

use Tests\Support\DatabaseTestCase;
use CodeIgniter\Test\ControllerTestTrait;

class UserWorkflowTest extends DatabaseTestCase
{
    use ControllerTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCompleteUserLoginToLogoutWorkflow(): void
    {
        // Step 1: Access login page
        $loginPageResult = $this->get('/auth/login');
        $loginPageResult->assertOK();
        $loginPageResult->assertSee('Login');

        // Step 2: Submit login credentials
        $loginData = [
            'username' => 'testadmin',
            'password' => 'testpass123',
        ];

        $loginResult = $this->post('/auth/authenticate', $loginData);
        $loginResult->assertRedirectTo('/dashboard');

        // Step 3: Verify session is established
        $session = session();
        $this->assertTrue($session->has('user_id'));
        $this->assertEquals('USR01', $session->get('user_id'));
        $this->assertEquals('testadmin', $session->get('username'));
        $this->assertEquals(1, $session->get('user_level'));

        // Step 4: Access dashboard
        $dashboardResult = $this->get('/dashboard');
        $dashboardResult->assertOK();
        $dashboardResult->assertSee('Dashboard');
        $dashboardResult->assertSee('testadmin'); // User name should be displayed

        // Step 5: Navigate to different modules
        $pengirimanResult = $this->get('/pengiriman');
        $pengirimanResult->assertOK();
        $pengirimanResult->assertSee('Daftar Pengiriman');

        $kategoriResult = $this->get('/kategori');
        $kategoriResult->assertOK();
        $kategoriResult->assertSee('Daftar Kategori');

        // Step 6: Change password
        $changePasswordData = [
            'current_password' => 'testpass123',
            'new_password' => 'newpassword123',
            'confirm_password' => 'newpassword123',
        ];

        $changePasswordResult = $this->post('/auth/change-password', $changePasswordData);
        $changePasswordResult->assertRedirectTo('/dashboard');

        $session = session();
        $this->assertTrue($session->has('success'));

        // Step 7: Logout
        $logoutResult = $this->get('/auth/logout');
        $logoutResult->assertRedirectTo('/auth/login');

        // Step 8: Verify session is cleared
        $this->assertFalse($session->has('user_id'));
        $this->assertFalse($session->has('username'));
        $this->assertFalse($session->has('user_level'));

        // Step 9: Verify cannot access protected pages after logout
        $protectedPageResult = $this->get('/dashboard');
        $protectedPageResult->assertRedirectTo('/auth/login');

        // Step 10: Login with new password
        $newLoginData = [
            'username' => 'testadmin',
            'password' => 'newpassword123',
        ];

        $newLoginResult = $this->post('/auth/authenticate', $newLoginData);
        $newLoginResult->assertRedirectTo('/dashboard');
    }

    public function testUserWorkflowWithDifferentRoles(): void
    {
        // Test Admin workflow
        $this->runRoleSpecificWorkflow('testadmin', 1, [
            '/dashboard',
            '/pengiriman',
            '/kategori',
            '/barang',
            '/pelanggan',
            '/kurir',
            '/user',
        ]);

        // Logout admin
        $this->get('/auth/logout');

        // Test Kurir workflow
        $this->runRoleSpecificWorkflow('testfinance', 2, [
            '/dashboard',
            '/pengiriman',
            '/pelanggan',
            '/kurir',
        ]);

        // Logout finance
        $this->get('/auth/logout');

        // Test Gudang workflow
        $this->runRoleSpecificWorkflow('testgudang', 3, [
            '/dashboard',
            '/pengiriman',
            '/kategori',
            '/barang',
            '/kurir',
        ]);
    }

    public function testUserSessionTimeout(): void
    {
        // Login
        $loginData = [
            'username' => 'testadmin',
            'password' => 'testpass123',
        ];

        $this->post('/auth/authenticate', $loginData);

        // Verify logged in
        $dashboardResult = $this->get('/dashboard');
        $dashboardResult->assertOK();

        // Simulate session timeout by destroying session
        session()->destroy();

        // Try to access protected page
        $protectedResult = $this->get('/pengiriman');
        $protectedResult->assertRedirectTo('/auth/login');

        // Verify session timeout message
        $session = session();
        $this->assertTrue($session->has('error') || $session->has('info'));
    }

    public function testInvalidLoginAttempts(): void
    {
        // Attempt 1: Wrong password
        $wrongPasswordData = [
            'username' => 'testadmin',
            'password' => 'wrongpassword',
        ];

        $result1 = $this->post('/auth/authenticate', $wrongPasswordData);
        $result1->assertRedirectTo('/auth/login');

        $session = session();
        $this->assertTrue($session->has('error'));
        $this->assertFalse($session->has('user_id'));

        // Attempt 2: Non-existent user
        $nonExistentUserData = [
            'username' => 'nonexistent',
            'password' => 'password123',
        ];

        $result2 = $this->post('/auth/authenticate', $nonExistentUserData);
        $result2->assertRedirectTo('/auth/login');

        $this->assertTrue($session->has('error'));
        $this->assertFalse($session->has('user_id'));

        // Attempt 3: Empty credentials
        $emptyData = [
            'username' => '',
            'password' => '',
        ];

        $result3 = $this->post('/auth/authenticate', $emptyData);
        $result3->assertRedirectTo('/auth/login');

        $this->assertTrue($session->has('error'));
        $this->assertFalse($session->has('user_id'));

        // Successful login after failed attempts
        $validData = [
            'username' => 'testadmin',
            'password' => 'testpass123',
        ];

        $successResult = $this->post('/auth/authenticate', $validData);
        $successResult->assertRedirectTo('/dashboard');

        $this->assertTrue($session->has('user_id'));
    }

    public function testUserNavigationFlow(): void
    {
        $this->loginAsUser('testadmin');

        // Start from dashboard
        $dashboardResult = $this->get('/dashboard');
        $dashboardResult->assertOK();

        // Navigate to pengiriman list
        $pengirimanListResult = $this->get('/pengiriman');
        $pengirimanListResult->assertOK();
        $pengirimanListResult->assertSee('Daftar Pengiriman');

        // View specific shipment
        $shipmentViewResult = $this->get('/pengiriman/view/PGR001');
        $shipmentViewResult->assertOK();
        $shipmentViewResult->assertSee('Detail Pengiriman');
        $shipmentViewResult->assertSee('PGR001');

        // Navigate to edit shipment
        $shipmentEditResult = $this->get('/pengiriman/edit/PGR001');
        $shipmentEditResult->assertOK();
        $shipmentEditResult->assertSee('Edit Pengiriman');

        // Navigate to create new shipment
        $createShipmentResult = $this->get('/pengiriman/create');
        $createShipmentResult->assertOK();
        $createShipmentResult->assertSee('Tambah Pengiriman');

        // Navigate to customer management
        $customerListResult = $this->get('/pelanggan');
        $customerListResult->assertOK();
        $customerListResult->assertSee('Daftar Pelanggan');

        // Navigate back to dashboard
        $backToDashboardResult = $this->get('/dashboard');
        $backToDashboardResult->assertOK();
        $backToDashboardResult->assertSee('Dashboard');
    }

    public function testUserPreferencesAndSettings(): void
    {
        $this->loginAsUser('testadmin');

        // Access user profile/settings
        $profileResult = $this->get('/auth/profile');
        if ($profileResult->isOK()) {
            $profileResult->assertSee('Profile');
            $profileResult->assertSee('testadmin');
        }

        // Change password workflow
        $changePasswordPageResult = $this->get('/auth/change-password');
        if ($changePasswordPageResult->isOK()) {
            $changePasswordPageResult->assertSee('Ubah Password');

            // Submit password change
            $passwordChangeData = [
                'current_password' => 'testpass123',
                'new_password' => 'newpassword456',
                'confirm_password' => 'newpassword456',
            ];

            $passwordChangeResult = $this->post('/auth/change-password', $passwordChangeData);
            $passwordChangeResult->assertRedirectTo('/dashboard');

            $session = session();
            $this->assertTrue($session->has('success'));
        }
    }

    /**
     * Helper method to run role-specific workflow
     */
    private function runRoleSpecificWorkflow(string $username, int $expectedLevel, array $allowedPages): void
    {
        // Login
        $loginData = [
            'username' => $username,
            'password' => 'testpass123',
        ];

        $loginResult = $this->post('/auth/authenticate', $loginData);
        $loginResult->assertRedirectTo('/dashboard');

        // Verify session
        $session = session();
        $this->assertEquals($expectedLevel, $session->get('user_level'));
        $this->assertEquals($username, $session->get('username'));

        // Test access to allowed pages
        foreach ($allowedPages as $page) {
            $result = $this->get($page);
            $this->assertTrue(
                $result->isOK() || ($result->isRedirect() && $result->getRedirectUrl() !== '/auth/login'),
                "User {$username} should be able to access {$page}"
            );
        }

        // Test dashboard access
        $dashboardResult = $this->get('/dashboard');
        $dashboardResult->assertOK();
        $dashboardResult->assertSee('Dashboard');
        $dashboardResult->assertSee($username);
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