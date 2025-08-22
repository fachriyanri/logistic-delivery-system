<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKategoriTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
            ],
            'keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id_kategori');
        $this->forge->createTable('kategori');
    }

    public function down()
    {
        $this->forge->dropTable('kategori');
    }
}