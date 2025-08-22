<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PelangganTestSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_pelanggan' => 'PLG001',
                'nama_pelanggan' => 'Test Customer 1',
                'alamat' => 'Test Address 1',
                'no_telp' => '081234567890',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_pelanggan' => 'PLG002',
                'nama_pelanggan' => 'Test Customer 2',
                'alamat' => 'Test Address 2',
                'no_telp' => '081234567891',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('pelanggan')->insertBatch($data);
    }
}