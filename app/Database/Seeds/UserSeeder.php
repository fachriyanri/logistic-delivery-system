<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_user'    => 'USR01',
                'username'   => 'adminpuninar',
                'password'   => password_hash('AdminPuninar123', PASSWORD_ARGON2ID),
                'level'      => USER_LEVEL_ADMIN,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_user'    => 'USR02',
                'username'   => 'kurirpuninar',
                'password'   => password_hash('KurirPuninar123', PASSWORD_ARGON2ID),
                'level'      => USER_LEVEL_COURIER,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_user'    => 'USR03',
                'username'   => 'gudangpuninar',
                'password'   => password_hash('GudangPuninar123', PASSWORD_ARGON2ID),
                'level'      => USER_LEVEL_GUDANG,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Simple Queries
        $this->db->table('user')->insertBatch($data);
    }
}