<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\SettingsEntity;

/**
 * Settings Model
 * 
 * Handles system settings stored in the database.
 * Provides methods to get, set, and manage application configuration.
 * 
 * @package App\Models
 */
class SettingsModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $returnType = SettingsEntity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'setting_key',
        'setting_value',
        'setting_type',
        'setting_group',
        'setting_description',
        'is_public'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'setting_key' => [
            'rules' => 'required|max_length[100]|is_unique[settings.setting_key,id,{id}]',
            'errors' => [
                'required' => 'Setting key is required',
                'max_length' => 'Setting key cannot exceed 100 characters',
                'is_unique' => 'Setting key already exists'
            ]
        ],
        'setting_type' => [
            'rules' => 'required|in_list[string,integer,boolean,json]',
            'errors' => [
                'required' => 'Setting type is required',
                'in_list' => 'Invalid setting type'
            ]
        ],
        'setting_group' => [
            'rules' => 'required|max_length[50]',
            'errors' => [
                'required' => 'Setting group is required',
                'max_length' => 'Setting group cannot exceed 50 characters'
            ]
        ]
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get setting by key
     */
    public function getByKey(string $key): ?SettingsEntity
    {
        return $this->where('setting_key', $key)->first();
    }

    /**
     * Get setting value by key with type casting
     */
    public function getValue(string $key, $default = null)
    {
        $setting = $this->getByKey($key);
        
        if (!$setting) {
            return $default;
        }

        return $setting->getTypedValue();
    }

    /**
     * Set setting value by key
     */
    public function setValue(string $key, $value): bool
    {
        $setting = $this->getByKey($key);
        
        if (!$setting) {
            return false;
        }

        $setting->setTypedValue($value);
        
        return $this->update($setting->id, ['setting_value' => $setting->setting_value]);
    }

    /**
     * Get settings by group
     */
    public function getByGroup(string $group): array
    {
        return $this->where('setting_group', $group)
                   ->orderBy('setting_key')
                   ->findAll();
    }

    /**
     * Get all public settings (accessible by non-admin users)
     */
    public function getPublicSettings(): array
    {
        return $this->where('is_public', 1)
                   ->orderBy('setting_group')
                   ->orderBy('setting_key')
                   ->findAll();
    }

    /**
     * Get settings grouped by group
     */
    public function getGroupedSettings(bool $publicOnly = false): array
    {
        $query = $this->orderBy('setting_group')->orderBy('setting_key');
        
        if ($publicOnly) {
            $query->where('is_public', 1);
        }
        
        $settings = $query->findAll();
        $grouped = [];
        
        foreach ($settings as $setting) {
            $grouped[$setting->setting_group][] = $setting;
        }
        
        return $grouped;
    }

    /**
     * Update multiple settings at once
     */
    public function updateMultiple(array $settings): bool
    {
        $this->db->transStart();
        
        foreach ($settings as $key => $value) {
            if (!$this->setValue($key, $value)) {
                $this->db->transRollback();
                return false;
            }
        }
        
        $this->db->transComplete();
        
        return $this->db->transStatus();
    }

    /**
     * Create new setting
     */
    public function createSetting(array $data): bool
    {
        // Validate required fields
        if (empty($data['setting_key']) || empty($data['setting_type'])) {
            return false;
        }

        // Set defaults
        $data['setting_group'] = $data['setting_group'] ?? 'general';
        $data['is_public'] = $data['is_public'] ?? 0;

        return $this->insert($data) !== false;
    }

    /**
     * Get database statistics for settings dashboard
     */
    public function getDatabaseStats(): array
    {
        $db = \Config\Database::connect();
        
        try {
            // Get database name
            $dbName = $db->getDatabase();
            
            // Get total tables count
            $tables = $db->listTables();
            $totalTables = count($tables);
            
            // Get database size (MySQL specific)
            $sizeQuery = "SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb'
                FROM information_schema.tables 
                WHERE table_schema = ?";
            
            $sizeResult = $db->query($sizeQuery, [$dbName])->getRow();
            $dbSize = $sizeResult ? $sizeResult->size_mb . ' MB' : 'Unknown';
            
            // Check connection status
            $connectionStatus = $db->connID ? 'Connected' : 'Disconnected';
            
            // Get record counts for main tables
            $tableCounts = [];
            $mainTables = ['user', 'pengiriman', 'barang', 'pelanggan', 'kurir', 'kategori'];
            
            foreach ($mainTables as $table) {
                if (in_array($table, $tables)) {
                    $count = $db->table($table)->countAll();
                    $tableCounts[$table] = $count;
                }
            }
            
            return [
                'database_name' => $dbName,
                'connection_status' => $connectionStatus,
                'total_tables' => $totalTables,
                'database_size' => $dbSize,
                'table_counts' => $tableCounts,
                'last_updated' => date('Y-m-d H:i:s')
            ];
            
        } catch (\Exception $e) {
            return [
                'database_name' => 'Unknown',
                'connection_status' => 'Error: ' . $e->getMessage(),
                'total_tables' => 0,
                'database_size' => 'Unknown',
                'table_counts' => [],
                'last_updated' => date('Y-m-d H:i:s')
            ];
        }
    }
}