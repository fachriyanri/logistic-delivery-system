<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailPengirimanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail' => [
                'type'           => 'INT',
                'constraint'     => 4,
                'auto_increment' => true,
            ],
            'id_pengiriman' => [
                'type'       => 'VARCHAR',
                'constraint' => 14,
            ],
            'id_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
            ],
            'qty' => [
                'type'       => 'INT',
                'constraint' => 4,
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

        $this->forge->addPrimaryKey('id_detail');
        $this->forge->addForeignKey('id_pengiriman', 'pengiriman', 'id_pengiriman', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_barang', 'barang', 'id_barang', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_pengiriman');
    }

    public function down()
    {
        $this->forge->dropTable('detail_pengiriman');
    }
}