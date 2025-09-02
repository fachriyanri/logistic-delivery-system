<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserRelationToKurir extends Migration
{
    public function up()
    {
        // Add id_user column to kurir table
        $this->forge->addColumn('kurir', [
            'id_user' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
                'after' => 'alamat'
            ]
        ]);

        // Add foreign key constraint
        $this->db->query('ALTER TABLE `kurir` ADD CONSTRAINT `kurir_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE');

        // Update existing data - map kurir records to users
        $db = \Config\Database::connect();
        
        $query = "
            UPDATE `kurir` k 
            INNER JOIN `user` u ON (
                u.level = 2 AND (
                    LOWER(REPLACE(k.nama, ' ', '')) = LOWER(u.username) OR
                    LOWER(k.nama) = LOWER(REPLACE(u.username, '_', ' '))
                )
            )
            SET k.id_user = u.id_user
        ";
        
        $db->query($query);
    }

    public function down()
    {
        // Drop foreign key
        $this->db->query('ALTER TABLE `kurir` DROP FOREIGN KEY `kurir_id_user_foreign`');
        
        // Drop column
        $this->forge->dropColumn('kurir', 'id_user');
    }
}