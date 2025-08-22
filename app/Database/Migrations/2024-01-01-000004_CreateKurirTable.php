<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKurirTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kurir' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'jenis_kelamin' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'telepon' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
            ],
            'alamat' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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

        $this->forge->addPrimaryKey('id_kurir');
        $this->forge->createTable('kurir');
    }

    public function down()
    {
        $this->forge->dropTable('kurir');
    }
}