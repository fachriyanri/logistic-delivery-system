<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailLocationToPengirimanTable extends Migration
{
    public function up()
    {
        $fields = [
            'detail_location' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'no_po'
            ]
        ];
        
        $this->forge->addColumn('pengiriman', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pengiriman', 'detail_location');
    }
}