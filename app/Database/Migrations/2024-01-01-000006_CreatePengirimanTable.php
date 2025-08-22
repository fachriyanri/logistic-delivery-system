<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengirimanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pengiriman' => [
                'type'       => 'VARCHAR',
                'constraint' => 14,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'id_pelanggan' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
            ],
            'id_kurir' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
            ],
            'no_kendaraan' => [
                'type'       => 'VARCHAR',
                'constraint' => 8,
            ],
            'no_po' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'penerima' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'photo' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
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

        $this->forge->addPrimaryKey('id_pengiriman');
        $this->forge->addForeignKey('id_pelanggan', 'pelanggan', 'id_pelanggan', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_kurir', 'kurir', 'id_kurir', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pengiriman');
    }

    public function down()
    {
        $this->forge->dropTable('pengiriman');
    }
}