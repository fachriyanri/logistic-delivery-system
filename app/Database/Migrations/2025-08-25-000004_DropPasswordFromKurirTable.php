<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropPasswordFromKurirTable extends Migration
{
    public function up()
    {
        // Drop password column from kurir table since we'll use user table for authentication
        $this->forge->dropColumn('kurir', 'password');
    }

    public function down()
    {
        // Add password column back if rollback is needed
        $this->forge->addColumn('kurir', [
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'after'      => 'alamat'
            ]
        ]);
    }
}