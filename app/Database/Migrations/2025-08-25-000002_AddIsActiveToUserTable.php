<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsActiveToUserTable extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('is_active', 'user')) {
            $fields = [
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => false,
                    'default'    => 1,
                    'after'      => 'level' // or wherever it should go
                ],
            ];
            $this->forge->addColumn('user', $fields);
        }
    }

    public function down()
    {
        // Make the down method robust too
        if ($this->db->fieldExists('is_active', 'user')) {
            $this->forge->dropColumn('user', 'is_active');
        }
    }
}
