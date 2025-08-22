<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KurirTestSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_kurir' => 'KUR001',
                'nama_kurir' => 'Test Courier 1',
                'alamat' => 'Test Courier Address 1',
                'no_telp' => '081234567892',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_kurir' => 'KUR002',
                'nama_kurir' => 'Test Courier 2',
                'alamat' => 'Test Courier Address 2',
                'no_telp' => '081234567893',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('kurir')->insertBatch($data);
    }
}