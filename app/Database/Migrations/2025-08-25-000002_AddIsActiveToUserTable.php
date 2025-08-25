<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsActiveToUserTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('user', [
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
                'after'      => 'level'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('user', 'is_active');
    }
}