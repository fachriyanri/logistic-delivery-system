<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'setting_type' => [
                'type' => 'ENUM',
                'constraint' => ['string', 'integer', 'boolean', 'json'],
                'default' => 'string',
            ],
            'setting_group' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'general',
            ],
            'setting_description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'is_public' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 if setting can be accessed by non-admin users',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('setting_key');
        $this->forge->addKey('setting_group');
        $this->forge->createTable('settings');

        // Insert default settings
        $data = [
            [
                'setting_key' => 'app_name',
                'setting_value' => 'PuninarLogistic',
                'setting_type' => 'string',
                'setting_group' => 'application',
                'setting_description' => 'Application name displayed throughout the system',
                'is_public' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key' => 'app_version',
                'setting_value' => '1.0.0',
                'setting_type' => 'string',
                'setting_group' => 'application',
                'setting_description' => 'Current application version',
                'is_public' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key' => 'timezone',
                'setting_value' => 'Asia/Jakarta',
                'setting_type' => 'string',
                'setting_group' => 'application',
                'setting_description' => 'System timezone',
                'is_public' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key' => 'company_name',
                'setting_value' => 'PT. Puninar Logistik Indonesia',
                'setting_type' => 'string',
                'setting_group' => 'company',
                'setting_description' => 'Company name for reports and documents',
                'is_public' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key' => 'company_address',
                'setting_value' => 'Jakarta, Indonesia',
                'setting_type' => 'string',
                'setting_group' => 'company',
                'setting_description' => 'Company address for reports and documents',
                'is_public' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key' => 'company_phone',
                'setting_value' => '+62-21-1234567',
                'setting_type' => 'string',
                'setting_group' => 'company',
                'setting_description' => 'Company phone number',
                'is_public' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key' => 'date_format',
                'setting_value' => 'd/m/Y',
                'setting_type' => 'string',
                'setting_group' => 'display',
                'setting_description' => 'Default date format for display',
                'is_public' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key' => 'items_per_page',
                'setting_value' => '15',
                'setting_type' => 'integer',
                'setting_group' => 'display',
                'setting_description' => 'Number of items to show per page in listings',
                'is_public' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key' => 'backup_enabled',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'setting_group' => 'system',
                'setting_description' => 'Enable automatic database backups',
                'is_public' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key' => 'maintenance_mode',
                'setting_value' => '0',
                'setting_type' => 'boolean',
                'setting_group' => 'system',
                'setting_description' => 'Enable maintenance mode',
                'is_public' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('settings')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}