<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengirimanTestSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_pengiriman' => 'PGR001',
                'tanggal' => date('Y-m-d'),
                'id_pelanggan' => 'PLG001',
                'id_kurir' => 'KUR001',
                'no_kendaraan' => 'B1234ABC',
                'no_po' => 'PO001',
                'keterangan' => 'Test shipment 1',
                'penerima' => 'Test Receiver 1',
                'photo' => null,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('pengiriman')->insertBatch($data);

        // Insert detail pengiriman
        $detailData = [
            [
                'id_detail' => 1,
                'id_pengiriman' => 'PGR001',
                'id_barang' => 'BRG001',
                'jumlah' => 10,
                'keterangan' => 'Test detail 1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('detail_pengiriman')->insertBatch($detailData);
    }
}