<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class SettingsEntity extends Entity
{
    protected $attributes = [
        'id' => null,
        'setting_key' => null,
        'setting_value' => null,
        'setting_type' => null,
        'setting_group' => null,
        'setting_description' => null,
        'is_public' => null,
        'created_at' => null,
        'updated_at' => null,
    ];

    protected $casts = [
        'id' => 'integer',
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $datamap = [];

    /**
     * Get the typed value based on setting_type
     */
    public function getTypedValue()
    {
        $value = $this->attributes['setting_value'];
        
        if ($value === null) {
            return null;
        }

        switch ($this->attributes['setting_type']) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'json':
                return json_decode($value, true);
            case 'string':
            default:
                return (string) $value;
        }
    }

    /**
     * Set value with proper type conversion
     */
    public function setTypedValue($value): self
    {
        switch ($this->attributes['setting_type']) {
            case 'boolean':
                $this->attributes['setting_value'] = $value ? '1' : '0';
                break;
            case 'integer':
                $this->attributes['setting_value'] = (string) (int) $value;
                break;
            case 'json':
                $this->attributes['setting_value'] = json_encode($value);
                break;
            case 'string':
            default:
                $this->attributes['setting_value'] = (string) $value;
                break;
        }
        
        return $this;
    }

    /**
     * Check if setting is public (accessible by non-admin users)
     */
    public function isPublic(): bool
    {
        return (bool) ($this->attributes['is_public'] ?? false);
    }

    /**
     * Get display name for setting key
     */
    public function getDisplayName(): string
    {
        $displayNames = [
            'app_name' => 'Application Name',
            'app_version' => 'Application Version',
            'timezone' => 'Timezone',
            'company_name' => 'Company Name',
            'company_address' => 'Company Address',
            'company_phone' => 'Company Phone',
            'date_format' => 'Date Format',
            'items_per_page' => 'Items Per Page',
            'backup_enabled' => 'Backup Enabled',
            'maintenance_mode' => 'Maintenance Mode',
        ];

        return $displayNames[$this->attributes['setting_key']] ?? ucwords(str_replace('_', ' ', $this->attributes['setting_key']));
    }

    /**
     * Get group display name
     */
    public function getGroupDisplayName(): string
    {
        $groupNames = [
            'application' => 'Application Settings',
            'company' => 'Company Information',
            'display' => 'Display Settings',
            'system' => 'System Settings',
            'general' => 'General Settings',
        ];

        return $groupNames[$this->attributes['setting_group']] ?? ucwords(str_replace('_', ' ', $this->attributes['setting_group']));
    }

    /**
     * Check if setting is editable based on key
     */
    public function isEditable(): bool
    {
        $readOnlySettings = ['app_version']; // These settings are not editable
        return !in_array($this->attributes['setting_key'], $readOnlySettings);
    }

    /**
     * Get input type for form rendering
     */
    public function getInputType(): string
    {
        switch ($this->attributes['setting_type']) {
            case 'boolean':
                return 'checkbox';
            case 'integer':
                return 'number';
            case 'json':
                return 'textarea';
            case 'string':
            default:
                // Special cases for string types
                if (strpos($this->attributes['setting_key'], 'email') !== false) {
                    return 'email';
                }
                if (strpos($this->attributes['setting_key'], 'phone') !== false) {
                    return 'tel';
                }
                if (strpos($this->attributes['setting_key'], 'url') !== false) {
                    return 'url';
                }
                return 'text';
        }
    }

    /**
     * Get validation rules for this setting
     */
    public function getValidationRules(): array
    {
        $rules = ['permit_empty'];

        switch ($this->attributes['setting_key']) {
            case 'app_name':
                $rules[] = 'min_length[1]|max_length[100]';
                break;
            case 'timezone':
                $rules[] = 'in_list[Asia/Jakarta,Asia/Makassar,Asia/Jayapura]';
                break;
            case 'company_phone':
                $rules[] = 'max_length[20]';
                break;
            case 'items_per_page':
                $rules[] = 'integer|greater_than[0]|less_than_equal_to[100]';
                break;
        }

        return $rules;
    }
}