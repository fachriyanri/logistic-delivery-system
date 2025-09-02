<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserTestSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_user' => 'USR01',
                'username' => 'testadmin',
                'password' => password_hash('testpass123', PASSWORD_ARGON2ID),
                'level' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_user' => 'USR02',
                'username' => 'testkurir',
                'password' => password_hash('testpass123', PASSWORD_ARGON2ID),
                'level' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_user' => 'USR03',
                'username' => 'testgudang',
                'password' => password_hash('testpass123', PASSWORD_ARGON2ID),
                'level' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('user')->insertBatch($data);
    }
}