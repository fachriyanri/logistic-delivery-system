<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KategoriTestSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_kategori' => 'KAT001',
                'nama_kategori' => 'Test Category 1',
                'keterangan' => 'Test category description 1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_kategori' => 'KAT002',
                'nama_kategori' => 'Test Category 2',
                'keterangan' => 'Test category description 2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('kategori')->insertBatch($data);
    }
}