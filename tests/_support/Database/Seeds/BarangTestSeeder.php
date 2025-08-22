<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BarangTestSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_barang' => 'BRG001',
                'nama_barang' => 'Test Item 1',
                'id_kategori' => 'KAT001',
                'satuan' => 'pcs',
                'keterangan' => 'Test item description 1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_barang' => 'BRG002',
                'nama_barang' => 'Test Item 2',
                'id_kategori' => 'KAT002',
                'satuan' => 'kg',
                'keterangan' => 'Test item description 2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('barang')->insertBatch($data);
    }
}