<?php

namespace App\Controllers;

use App\Services\SettingsService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Settings Controller
 * 
 * Handles system settings management for administrators.
 * Provides interfaces for managing application configuration,
 * company information, and system preferences.
 * 
 * @package App\Controllers
 */
class SettingsController extends BaseController
{
    protected SettingsService $settingsService;

    public function __construct()
    {
        $this->settingsService = new SettingsService();
    }

    /**
     * Display settings page
     */
    public function index(): string
    {
        // Only admin can access settings
        if (session()->get('level') != 1) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // Check if settings table exists
        $settingsStatus = $this->settingsService->getSettingsStatus();
        
        // Debug: Log settings status
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'Settings status: ' . json_encode($settingsStatus));
        }
        
        $data = [
            'title' => 'Pengaturan Sistem',
            'active_menu' => 'settings',
            'settings_status' => $settingsStatus,
            'application_settings' => $this->settingsService->getApplicationSettings(),
            'company_settings' => $this->settingsService->getCompanySettings(),
            'display_settings' => $this->settingsService->getDisplaySettings(),
            'system_settings' => $this->settingsService->getSystemSettings(),
            'database_stats' => $this->settingsService->getDatabaseStats(),
            'system_info' => $this->settingsService->getSystemInfo(),
            'timezone_options' => $this->settingsService->getTimezoneOptions(),
            'date_format_options' => $this->settingsService->getDateFormatOptions(),
        ];
        
        // Debug: Log application settings
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'Application settings: ' . json_encode($data['application_settings']));
        }

        return view('settings/index', $data);
    }

    /**
     * Update settings
     */
    public function update(): ResponseInterface
    {
        // Only admin can update settings
        if (session()->get('level') != 1) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses fitur ini.');
        }

        $post = $this->request->getPost();
        
        if (!$post) {
            return redirect()->back()->with('error', 'Data tidak valid');
        }

        // Debug: Log the POST data (remove in production)
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'Settings POST data: ' . json_encode($post));
            log_message('debug', 'Settings table exists: ' . ($this->settingsService->settingsTableExists() ? 'yes' : 'no'));
        }

        // Get action first
        $action = $post['action'] ?? 'save';

        // Remove CSRF token and other non-setting fields
        $fieldsToRemove = ['csrf_token', 'action'];
        foreach ($fieldsToRemove as $field) {
            unset($post[$field]);
        }
        
        // Only process known setting keys to avoid processing unwanted fields
        $validSettingKeys = [
            'app_name', 'timezone', 'company_name', 'company_address', 'company_phone',
            'date_format', 'items_per_page', 'backup_enabled', 'maintenance_mode'
        ];
        
        $filteredPost = [];
        foreach ($post as $key => $value) {
            if (in_array($key, $validSettingKeys)) {
                $filteredPost[$key] = $value;
            }
        }
        
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'Filtered settings data: ' . json_encode($filteredPost));
        }

        // Handle different actions
        switch ($action) {
            case 'test_connection':
                return $this->testDatabaseConnection();
            case 'backup_settings':
                return $this->backupSettings();
            case 'clear_cache':
                return $this->clearCache();
            case 'save':
            default:
                return $this->saveSettings($filteredPost);
        }
    }

    /**
     * Save settings
     */
    private function saveSettings(array $settings): ResponseInterface
    {
        // Check if settings table exists
        if (!$this->settingsService->settingsTableExists()) {
            session()->setFlashdata('error', 'Settings table does not exist. Please run the migration first.');
            return redirect()->to('/settings');
        }

        // Check if we have any settings to process
        if (empty($settings)) {
            session()->setFlashdata('error', 'No settings data received to update.');
            return redirect()->to('/settings');
        }

        $result = $this->settingsService->updateSettings($settings);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            $errorMessage = $result['message'];
            if (!empty($result['errors'])) {
                $errorMessage .= '<br>' . implode('<br>', $result['errors']);
            }
            session()->setFlashdata('error', $errorMessage);
        }

        return redirect()->to('/settings');
    }

    /**
     * Test database connection
     */
    private function testDatabaseConnection(): ResponseInterface
    {
        $result = $this->settingsService->testDatabaseConnection();

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/settings');
    }

    /**
     * Backup settings to file
     */
    private function backupSettings(): ResponseInterface
    {
        $result = $this->settingsService->backupSettings();

        if ($result['success']) {
            session()->setFlashdata('success', $result['message'] . ' (File: ' . $result['filename'] . ')');
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/settings');
    }

    /**
     * Clear application cache
     */
    private function clearCache(): ResponseInterface
    {
        try {
            // Clear settings cache
            $this->settingsService->clearCache();
            
            // Clear CodeIgniter cache if available
            if (function_exists('cache')) {
                cache()->clean();
            }
            
            session()->setFlashdata('success', 'Cache cleared successfully');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Failed to clear cache: ' . $e->getMessage());
        }

        return redirect()->to('/settings');
    }

    /**
     * AJAX endpoint for getting database stats
     */
    public function getDatabaseStats(): ResponseInterface
    {
        // Only admin can access
        if (session()->get('level') != 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        $stats = $this->settingsService->getDatabaseStats();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * AJAX endpoint for getting system info
     */
    public function getSystemInfo(): ResponseInterface
    {
        // Only admin can access
        if (session()->get('level') != 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        $info = $this->settingsService->getSystemInfo();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $info
        ]);
    }

    /**
     * Run database migration (admin only)
     */
    public function runMigration(): ResponseInterface
    {
        // Only admin can run migrations
        if (session()->get('level') != 1) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        try {
            $migrate = \Config\Services::migrations();
            $migrate->latest();
            
            session()->setFlashdata('success', 'Migration completed successfully! Settings table has been created.');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Migration failed: ' . $e->getMessage());
        }

        return redirect()->to('/settings');
    }
}