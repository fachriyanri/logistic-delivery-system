<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_kategori' => 'KTG01',
                'nama'        => 'KATEGORI 1',
                'keterangan'  => 'KATEGORI 1',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id_kategori' => 'KTG02',
                'nama'        => 'KATEGORI 2',
                'keterangan'  => 'KATEGORI 2',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id_kategori' => 'KTG03',
                'nama'        => 'KATEGORI 3',
                'keterangan'  => 'KATEGORI 3',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('kategori')->insertBatch($data);
    }
}