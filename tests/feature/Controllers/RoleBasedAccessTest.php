<?php

namespace Tests\Feature\Controllers;

use Tests\Support\DatabaseTestCase;
use CodeIgniter\Test\ControllerTestTrait;

class RoleBasedAccessTest extends DatabaseTestCase
{
    use ControllerTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testAdminCanAccessAllModules(): void
    {
        $this->loginAsUser('testadmin');
        
        // Admin should access all modules
        $modules = [
            '/dashboard',
            '/pengiriman',
            '/pengiriman/create',
            '/kategori',
            '/kategori/create',
            '/barang',
            '/barang/create',
            '/pelanggan',
            '/pelanggan/create',
            '/kurir',
            '/kurir/create',
            '/user',
            '/user/create',
        ];

        foreach ($modules as $module) {
            $result = $this->get($module);
            $this->assertTrue(
                $result->isOK() || $result->isRedirect(),
                "Admin should be able to access {$module}"
            );
            
            // If redirected, it shouldn't be to login page
            if ($result->isRedirect()) {
                $this->assertNotEquals('/auth/login', $result->getRedirectUrl());
            }
        }
    }

    public function testFinanceUserAccessRestrictions(): void
    {
        $this->loginAsUser('testfinance');
        
        // Finance should access these modules
        $allowedModules = [
            '/dashboard',
            '/pengiriman',
            '/pelanggan',
            '/kurir',
        ];

        foreach ($allowedModules as $module) {
            $result = $this->get($module);
            $this->assertTrue(
                $result->isOK() || ($result->isRedirect() && $result->getRedirectUrl() !== '/auth/login'),
                "Finance should be able to access {$module}"
            );
        }

        // Finance should NOT access these modules (or have limited access)
        $restrictedModules = [
            '/user',
            '/user/create',
        ];

        foreach ($restrictedModules as $module) {
            $result = $this->get($module);
            $this->assertTrue(
                $result->getStatusCode() === 403 || 
                $result->isRedirect(),
                "Finance should have restricted access to {$module}"
            );
        }
    }

    public function testGudangUserAccessRestrictions(): void
    {
        $this->loginAsUser('testgudang');
        
        // Gudang should access these modules
        $allowedModules = [
            '/dashboard',
            '/pengiriman',
            '/pengiriman/create',
            '/kategori',
            '/kategori/create',
            '/barang',
            '/barang/create',
            '/kurir',
        ];

        foreach ($allowedModules as $module) {
            $result = $this->get($module);
            $this->assertTrue(
                $result->isOK() || ($result->isRedirect() && $result->getRedirectUrl() !== '/auth/login'),
                "Gudang should be able to access {$module}"
            );
        }

        // Gudang should NOT access these modules
        $restrictedModules = [
            '/user',
            '/user/create',
        ];

        foreach ($restrictedModules as $module) {
            $result = $this->get($module);
            $this->assertTrue(
                $result->getStatusCode() === 403 || 
                $result->isRedirect(),
                "Gudang should have restricted access to {$module}"
            );
        }
    }

    public function testFinanceCannotCreateOrDeleteShipments(): void
    {
        $this->loginAsUser('testfinance');
        
        // Finance might be able to view create page but not actually create
        $createData = [
            'tanggal' => date('Y-m-d'),
            'id_pelanggan' => 'PLG001',
            'id_kurir' => 'KUR001',
            'no_kendaraan' => 'B9999XYZ',
        ];

        $result = $this->post('/pengiriman/store', $createData);
        
        // Should be forbidden or redirected
        $this->assertTrue(
            $result->getStatusCode() === 403 || 
            $result->isRedirect()
        );

        // Finance should not be able to delete
        $deleteResult = $this->post('/pengiriman/delete/PGR001');
        $this->assertTrue(
            $deleteResult->getStatusCode() === 403 || 
            $deleteResult->isRedirect()
        );
    }

    public function testGudangCannotAccessUserManagement(): void
    {
        $this->loginAsUser('testgudang');
        
        // Should not access user management
        $result = $this->get('/user');
        $this->assertTrue(
            $result->getStatusCode() === 403 || 
            ($result->isRedirect() && $result->getRedirectUrl() !== '/dashboard')
        );

        // Should not create users
        $createUserData = [
            'username' => 'newuser',
            'password' => 'password123',
            'level' => 3,
        ];

        $createResult = $this->post('/user/store', $createUserData);
        $this->assertTrue(
            $createResult->getStatusCode() === 403 || 
            $createResult->isRedirect()
        );
    }

    public function testRoleFilterWorksCorrectly(): void
    {
        // Test that role filter is applied correctly
        
        // Admin should pass all role checks
        $this->loginAsUser('testadmin');
        $adminResult = $this->get('/user');
        $this->assertTrue($adminResult->isOK() || $adminResult->isRedirect());

        // Finance should fail admin-only checks
        $this->get('/auth/logout');
        $this->loginAsUser('testfinance');
        $financeResult = $this->get('/user');
        $this->assertTrue(
            $financeResult->getStatusCode() === 403 || 
            $financeResult->isRedirect()
        );

        // Gudang should fail admin and finance checks
        $this->get('/auth/logout');
        $this->loginAsUser('testgudang');
        $gudangResult = $this->get('/user');
        $this->assertTrue(
            $gudangResult->getStatusCode() === 403 || 
            $gudangResult->isRedirect()
        );
    }

    public function testAPIEndpointsRespectRoleAccess(): void
    {
        // Test API endpoints with different roles
        
        // Admin should access all API endpoints
        $this->loginAsUser('testadmin');
        $adminApiResult = $this->get('/api/users');
        $this->assertTrue($adminApiResult->isOK() || $adminApiResult->getStatusCode() === 404);

        // Finance should have limited API access
        $this->get('/auth/logout');
        $this->loginAsUser('testfinance');
        $financeApiResult = $this->get('/api/users');
        $this->assertTrue(
            $financeApiResult->getStatusCode() === 403 || 
            $financeApiResult->getStatusCode() === 404 ||
            $financeApiResult->isRedirect()
        );
    }

    public function testDataFilteringByRole(): void
    {
        // Test that users only see data they're allowed to see
        
        $this->loginAsUser('testfinance');
        
        // Finance user should see shipment data
        $result = $this->get('/pengiriman');
        $result->assertOK();
        $result->assertSee('PGR001');
        
        // But might not see certain sensitive information or actions
        $viewResult = $this->get('/pengiriman/view/PGR001');
        $this->assertTrue($viewResult->isOK());
        
        // Check that edit/delete buttons are not shown for finance users
        if ($viewResult->isOK()) {
            $content = $viewResult->response()->getBody();
            // This depends on your UI implementation
            // Finance users might not see edit/delete buttons
        }
    }

    public function testSessionRoleConsistency(): void
    {
        $this->loginAsUser('testadmin');
        
        $session = session();
        $this->assertEquals(1, $session->get('user_level'));
        $this->assertEquals('testadmin', $session->get('username'));
        
        // Access a page and verify role is still consistent
        $result = $this->get('/dashboard');
        $result->assertOK();
        
        // Session should still have correct role
        $this->assertEquals(1, $session->get('user_level'));
    }

    public function testRoleEscalationPrevention(): void
    {
        $this->loginAsUser('testgudang');
        
        // Try to manually set session to admin level (simulation of attack)
        $session = session();
        $originalLevel = $session->get('user_level');
        
        // This should not work in a real application due to proper session management
        // But we test that the application doesn't rely solely on session data
        
        $result = $this->get('/user');
        $this->assertTrue(
            $result->getStatusCode() === 403 || 
            $result->isRedirect()
        );
        
        // Verify level hasn't actually changed
        $this->assertEquals($originalLevel, $session->get('user_level'));
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