<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrateExistingData extends Migration
{
    public function up()
    {
        // This migration assumes the old data exists in tables with '_old' suffix
        // or in a backup database. Adjust the source table names as needed.
        
        $this->migrateKategoriData();
        $this->migrateBarangData();
        $this->migratePelangganData();
        $this->migrateKurirData();
        $this->migratePengirimanData();
        $this->migrateDetailPengirimanData();
    }

    public function down()
    {
        // Clear migrated data (be careful with this in production)
        $this->db->table('detail_pengiriman')->truncate();
        $this->db->table('pengiriman')->truncate();
        $this->db->table('kurir')->truncate();
        $this->db->table('pelanggan')->truncate();
        $this->db->table('barang')->truncate();
        $this->db->table('kategori')->truncate();
    }

    private function migrateKategoriData()
    {
        // Check if old data exists (assuming backup table or different database)
        if ($this->db->tableExists('kategori_backup')) {
            $oldData = $this->db->table('kategori_backup')->get()->getResultArray();
        } else {
            // Default kategori data from the SQL dump
            $oldData = [
                ['id_kategori' => 'KTG01', 'nama' => 'KATEGORI 1', 'keterangan' => 'KATEGORI 1'],
                ['id_kategori' => 'KTG02', 'nama' => 'KATEGORI 2', 'keterangan' => 'KATEGORI 2'],
                ['id_kategori' => 'KTG03', 'nama' => 'KATEGORI 3', 'keterangan' => 'KATEGORI 3'],
            ];
        }

        foreach ($oldData as $row) {
            $newRow = [
                'id_kategori' => $row['id_kategori'],
                'nama' => $row['nama'],
                'keterangan' => $row['keterangan'] ?? null,
            ];

            // Check if record already exists
            $existing = $this->db->table('kategori')
                ->where('id_kategori', $newRow['id_kategori'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('kategori')->insert($newRow);
            }
        }
    }

    private function migrateBarangData()
    {
        if ($this->db->tableExists('barang_backup')) {
            $oldData = $this->db->table('barang_backup')->get()->getResultArray();
        } else {
            // Sample data from SQL dump - in real scenario, this would come from backup
            $oldData = [
                ['id_barang' => 'BRG0001', 'nama' => 'BRAKE SHOE HONDA ASP', 'satuan' => 'SATUAN 1', 'del_no' => 'Box', 'id_kategori' => 'KTG01'],
                ['id_barang' => 'BRG0002', 'nama' => 'BRAKE SHOE KHARISMA', 'satuan' => 'SATUAN 1', 'del_no' => 'Box', 'id_kategori' => 'KTG02'],
                ['id_barang' => 'BRG0003', 'nama' => 'BRAKE SHOE SUPRA FED', 'satuan' => 'SATUAN 1', 'del_no' => 'Box', 'id_kategori' => 'KTG01'],
                // Add more sample data as needed
            ];
        }

        foreach ($oldData as $row) {
            $newRow = [
                'id_barang' => $row['id_barang'],
                'nama' => $row['nama'],
                'satuan' => $row['satuan'],
                'del_no' => $row['del_no'],
                'id_kategori' => $row['id_kategori'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $existing = $this->db->table('barang')
                ->where('id_barang', $newRow['id_barang'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('barang')->insert($newRow);
            }
        }
    }

    private function migratePelangganData()
    {
        if ($this->db->tableExists('pelanggan_backup')) {
            $oldData = $this->db->table('pelanggan_backup')->get()->getResultArray();
        } else {
            $oldData = [
                ['id_pelanggan' => 'CST0001', 'nama' => 'ASTRA OTOPART', 'telepon' => '021-4603550', 'alamat' => 'jakarta'],
                ['id_pelanggan' => 'CST0002', 'nama' => 'Idemitsu Lube Indonesia', 'telepon' => '021-8911 4611', 'alamat' => 'JL Permata Raya, Kawasan Industri KIIC, Lot BB/4A, Karawang, Jawa Barat,'],
                ['id_pelanggan' => 'CST0003', 'nama' => 'Federal Karyatama', 'telepon' => '021-4613583', 'alamat' => 'Jl. Pulobuaran Raya, RW.9, Jatinegara, Cakung, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13910'],
            ];
        }

        foreach ($oldData as $row) {
            $newRow = [
                'id_pelanggan' => $row['id_pelanggan'],
                'nama' => $row['nama'],
                'telepon' => $row['telepon'],
                'alamat' => $row['alamat'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $existing = $this->db->table('pelanggan')
                ->where('id_pelanggan', $newRow['id_pelanggan'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('pelanggan')->insert($newRow);
            }
        }
    }

    private function migrateKurirData()
    {
        if ($this->db->tableExists('kurir_backup')) {
            $oldData = $this->db->table('kurir_backup')->get()->getResultArray();
        } else {
            $oldData = [
                ['id_kurir' => 'KRR01', 'nama' => 'EKO', 'jenis_kelamin' => 'Laki-Laki', 'telepon' => '081385195955', 'alamat' => 'TANGERANG', 'password' => 'ee9cc68e583241dcb548e4217d8c8eb9'],
                ['id_kurir' => 'KRR02', 'nama' => 'ERIK', 'jenis_kelamin' => 'Laki-Laki', 'telepon' => '081284959589', 'alamat' => 'TANGERANG', 'password' => '6faae43d506a230beedcdbff231b478e'],
                ['id_kurir' => 'KRR03', 'nama' => 'TRIBUDI', 'jenis_kelamin' => 'Laki-Laki', 'telepon' => '081219900381', 'alamat' => 'TANGERANG', 'password' => 'b4ae1f68447e3df8a1ce6c4dc3660c5b'],
            ];
        }

        foreach ($oldData as $row) {
            // Convert old MD5 passwords to new secure hashing
            $newPassword = password_hash('defaultpassword123', PASSWORD_ARGON2ID);
            
            $newRow = [
                'id_kurir' => $row['id_kurir'],
                'nama' => $row['nama'],
                'jenis_kelamin' => $row['jenis_kelamin'],
                'telepon' => $row['telepon'],
                'alamat' => $row['alamat'] ?? null,
                'password' => $newPassword, // New secure password
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $existing = $this->db->table('kurir')
                ->where('id_kurir', $newRow['id_kurir'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('kurir')->insert($newRow);
            }
        }
    }

    private function migratePengirimanData()
    {
        if ($this->db->tableExists('pengiriman_backup')) {
            $oldData = $this->db->table('pengiriman_backup')->get()->getResultArray();
        } else {
            $oldData = [
                [
                    'id_pengiriman' => 'KRM20160820001',
                    'tanggal' => '2016-08-20',
                    'id_pelanggan' => 'CST0001',
                    'id_kurir' => 'KRR01',
                    'no_kendaraan' => 'B021ZIG',
                    'no_po' => '8732984732984',
                    'keterangan' => '',
                    'penerima' => '',
                    'photo' => '',
                    'status' => 1
                ]
            ];
        }

        foreach ($oldData as $row) {
            $newRow = [
                'id_pengiriman' => $row['id_pengiriman'],
                'tanggal' => $row['tanggal'],
                'id_pelanggan' => $row['id_pelanggan'],
                'id_kurir' => $row['id_kurir'],
                'no_kendaraan' => $row['no_kendaraan'],
                'no_po' => $row['no_po'],
                'keterangan' => $row['keterangan'] ?: null,
                'penerima' => $row['penerima'] ?: null,
                'photo' => $row['photo'] ?: null,
                'status' => (int)$row['status'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $existing = $this->db->table('pengiriman')
                ->where('id_pengiriman', $newRow['id_pengiriman'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('pengiriman')->insert($newRow);
            }
        }
    }

    private function migrateDetailPengirimanData()
    {
        if ($this->db->tableExists('detail_pengiriman_backup')) {
            $oldData = $this->db->table('detail_pengiriman_backup')->get()->getResultArray();
        } else {
            $oldData = [
                ['id_detail' => 1, 'id_pengiriman' => 'KRM20160820001', 'id_barang' => 'BRG0001', 'qty' => 1],
                ['id_detail' => 2, 'id_pengiriman' => 'KRM20160820001', 'id_barang' => 'BRG0002', 'qty' => 3]
            ];
        }

        foreach ($oldData as $row) {
            $newRow = [
                'id_pengiriman' => $row['id_pengiriman'],
                'id_barang' => $row['id_barang'],
                'qty' => (int)$row['qty'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Check if this detail already exists
            $existing = $this->db->table('detail_pengiriman')
                ->where('id_pengiriman', $newRow['id_pengiriman'])
                ->where('id_barang', $newRow['id_barang'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('detail_pengiriman')->insert($newRow);
            }
        }
    }
}