<?php

namespace App\Services;

use App\Models\SettingsModel;
use App\Entities\SettingsEntity;

/**
 * Settings Service
 * 
 * Business logic layer for system settings management.
 * Handles settings operations, validation, and caching.
 * 
 * @package App\Services
 */
class SettingsService
{
    protected SettingsModel $settingsModel;
    protected \CodeIgniter\Database\BaseConnection $db;
    protected array $cache = [];

    public function __construct()
    {
        $this->settingsModel = model('SettingsModel');
        $this->db = \Config\Database::connect();
    }

    /**
     * Get all settings grouped by category
     */
    public function getAllGroupedSettings(bool $publicOnly = false): array
    {
        return $this->settingsModel->getGroupedSettings($publicOnly);
    }

    /**
     * Get setting value with caching
     */
    public function getSetting(string $key, $default = null)
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $value = $this->settingsModel->getValue($key, $default);
        $this->cache[$key] = $value;

        return $value;
    }

    /**
     * Update multiple settings with validation
     */
    public function updateSettings(array $settings): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'errors' => []
        ];

        // Validate all settings first
        $validationErrors = [];
        $validSettings = [];
        
        foreach ($settings as $key => $value) {
            $setting = $this->settingsModel->getByKey($key);
            
            if (!$setting) {
                $validationErrors[] = "Setting '$key' not found";
                continue;
            }

            if (!$setting->isEditable()) {
                $validationErrors[] = "Setting '$key' is not editable";
                continue;
            }

            // Add to valid settings for processing
            $validSettings[$key] = $value;

            // Validate value based on setting rules
            $rules = $setting->getValidationRules();
            if (!empty($rules)) {
                $validation = \Config\Services::validation();
                // Convert array of rules to pipe-separated string
                $ruleString = is_array($rules) ? implode('|', $rules) : $rules;
                $validation->setRules([$key => $ruleString]);
                
                if (!$validation->run([$key => $value])) {
                    $validationErrors[] = "Setting '$key': " . implode(', ', $validation->getErrors());
                }
            }
        }

        if (!empty($validationErrors)) {
            $result['errors'] = $validationErrors;
            $result['message'] = 'Validation failed for some settings';
            return $result;
        }

        // Check if we have any valid settings to update
        if (empty($validSettings)) {
            $result['message'] = 'No valid settings found to update';
            return $result;
        }

        // Update settings (only valid ones)
        try {
            $updated = $this->settingsModel->updateMultiple($validSettings);
            
            if ($updated) {
                // Clear cache
                $this->cache = [];
                
                $result['success'] = true;
                $result['message'] = 'Settings updated successfully (' . count($validSettings) . ' settings)';
            } else {
                $result['message'] = 'Failed to update settings';
            }
        } catch (\Exception $e) {
            $result['message'] = 'Error updating settings: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Get system information for dashboard
     */
    public function getSystemInfo(): array
    {
        return [
            'php_version' => phpversion(),
            'codeigniter_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'timezone' => date_default_timezone_get(),
            'current_time' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get database statistics
     */
    public function getDatabaseStats(): array
    {
        return $this->settingsModel->getDatabaseStats();
    }

    /**
     * Get application settings for display
     */
    public function getApplicationSettings(): array
    {
        try {
            $settings = $this->settingsModel->getByGroup('application');
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->setting_key] = [
                    'value' => $setting->getTypedValue(),
                    'display_name' => $setting->getDisplayName(),
                    'description' => $setting->setting_description,
                    'editable' => $setting->isEditable(),
                    'input_type' => $setting->getInputType(),
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            // Return empty array if settings table doesn't exist
            return [];
        }
    }

    /**
     * Get company settings for display
     */
    public function getCompanySettings(): array
    {
        try {
            $settings = $this->settingsModel->getByGroup('company');
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->setting_key] = [
                    'value' => $setting->getTypedValue(),
                    'display_name' => $setting->getDisplayName(),
                    'description' => $setting->setting_description,
                    'editable' => $setting->isEditable(),
                    'input_type' => $setting->getInputType(),
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            // Return empty array if settings table doesn't exist
            return [];
        }
    }

    /**
     * Get display settings for forms
     */
    public function getDisplaySettings(): array
    {
        try {
            $settings = $this->settingsModel->getByGroup('display');
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->setting_key] = [
                    'value' => $setting->getTypedValue(),
                    'display_name' => $setting->getDisplayName(),
                    'description' => $setting->setting_description,
                    'editable' => $setting->isEditable(),
                    'input_type' => $setting->getInputType(),
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            // Return empty array if settings table doesn't exist
            return [];
        }
    }

    /**
     * Get system settings (admin only)
     */
    public function getSystemSettings(): array
    {
        try {
            $settings = $this->settingsModel->getByGroup('system');
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->setting_key] = [
                    'value' => $setting->getTypedValue(),
                    'display_name' => $setting->getDisplayName(),
                    'description' => $setting->setting_description,
                    'editable' => $setting->isEditable(),
                    'input_type' => $setting->getInputType(),
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            // Return empty array if settings table doesn't exist
            return [];
        }
    }

    /**
     * Check if settings table exists
     */
    public function settingsTableExists(): bool
    {
        try {
            $this->db->query("SELECT 1 FROM settings LIMIT 1");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get settings table status and migration info
     */
    public function getSettingsStatus(): array
    {
        $tableExists = $this->settingsTableExists();
        
        return [
            'table_exists' => $tableExists,
            'migration_needed' => !$tableExists,
            'migration_command' => 'php spark migrate',
            'message' => $tableExists ? 
                'Settings table is ready' : 
                'Settings table not found. Please run migration: php spark migrate'
        ];
    }

    /**
     * Test database connection
     */
    public function testDatabaseConnection(): array
    {
        try {
            $this->db->query('SELECT 1');
            return [
                'success' => true,
                'message' => 'Database connection successful',
                'status' => 'Connected'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage(),
                'status' => 'Disconnected'
            ];
        }
    }

    /**
     * Backup settings to file
     */
    public function backupSettings(): array
    {
        try {
            $settings = $this->settingsModel->findAll();
            $data = [];
            
            foreach ($settings as $setting) {
                $data[] = [
                    'setting_key' => $setting->setting_key,
                    'setting_value' => $setting->setting_value,
                    'setting_type' => $setting->setting_type,
                    'setting_group' => $setting->setting_group,
                    'setting_description' => $setting->setting_description,
                    'is_public' => $setting->is_public,
                ];
            }
            
            $filename = 'settings_backup_' . date('Y-m-d_H-i-s') . '.json';
            $filepath = WRITEPATH . 'backups/' . $filename;
            
            // Create backups directory if it doesn't exist
            if (!is_dir(WRITEPATH . 'backups/')) {
                mkdir(WRITEPATH . 'backups/', 0755, true);
            }
            
            file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
            
            return [
                'success' => true,
                'message' => 'Settings backed up successfully',
                'filename' => $filename
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        $this->cache = [];
    }

    /**
     * Get timezone options
     */
    public function getTimezoneOptions(): array
    {
        return [
            'Asia/Jakarta' => 'Asia/Jakarta (WIB)',
            'Asia/Makassar' => 'Asia/Makassar (WITA)',
            'Asia/Jayapura' => 'Asia/Jayapura (WIT)',
        ];
    }

    /**
     * Get date format options
     */
    public function getDateFormatOptions(): array
    {
        return [
            'd/m/Y' => 'DD/MM/YYYY (31/12/2024)',
            'm/d/Y' => 'MM/DD/YYYY (12/31/2024)',
            'Y-m-d' => 'YYYY-MM-DD (2024-12-31)',
            'd-m-Y' => 'DD-MM-YYYY (31-12-2024)',
        ];
    }
}