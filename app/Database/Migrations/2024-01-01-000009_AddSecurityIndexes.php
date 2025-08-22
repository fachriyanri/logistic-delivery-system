<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSecurityIndexes extends Migration
{
    /**
     * Adds indexes to various tables to improve query performance.
     *
     * This method uses raw SQL queries to create indexes, which is a reliable
     * way to add them to existing tables in CodeIgniter 4.
     */
    public function up()
    {
        // User table indexes
        if ($this->db->tableExists('user')) {
            $this->db->query('CREATE INDEX idx_user_username ON user(username)');
            $this->db->query('CREATE INDEX idx_user_level ON user(level)');
        }

        // Pengiriman table indexes
        if ($this->db->tableExists('pengiriman')) {
            $this->db->query('CREATE INDEX idx_pengiriman_tanggal ON pengiriman(tanggal)');
            $this->db->query('CREATE INDEX idx_pengiriman_pelanggan ON pengiriman(id_pelanggan)');
            $this->db->query('CREATE INDEX idx_pengiriman_kurir ON pengiriman(id_kurir)');
            $this->db->query('CREATE INDEX idx_pengiriman_status ON pengiriman(status)');
            $this->db->query('CREATE INDEX idx_pengiriman_date_status ON pengiriman(tanggal, status)');
        }

        // Detail pengiriman table indexes
        if ($this->db->tableExists('detail_pengiriman')) {
            $this->db->query('CREATE INDEX idx_detail_pengiriman ON detail_pengiriman(id_pengiriman)');
            $this->db->query('CREATE INDEX idx_detail_barang ON detail_pengiriman(id_barang)');
        }

        // Barang table indexes
        if ($this->db->tableExists('barang')) {
            $this->db->query('CREATE INDEX idx_barang_kategori ON barang(id_kategori)');
            $this->db->query('CREATE INDEX idx_barang_nama ON barang(nama)');
        }

        // Pelanggan table indexes
        if ($this->db->tableExists('pelanggan')) {
            $this->db->query('CREATE INDEX idx_pelanggan_nama ON pelanggan(nama)');
        }

        // Kurir table indexes
        if ($this->db->tableExists('kurir')) {
            $this->db->query('CREATE INDEX idx_kurir_nama ON kurir(nama)');
        }

        // Kategori table indexes
        if ($this->db->tableExists('kategori')) {
            $this->db->query('CREATE INDEX idx_kategori_nama ON kategori(nama)');
        }
    }

    /**
     * Reverts the changes performed in the up() method.
     *
     * This method uses the Forge class's dropKey() method, which is the
     * correct way to remove indexes.
     */
    public function down()
    {
        // User table indexes
        if ($this->db->tableExists('user')) {
            $this->forge->dropKey('user', 'idx_user_username');
            $this->forge->dropKey('user', 'idx_user_level');
        }

        // Pengiriman table indexes
        if ($this->db->tableExists('pengiriman')) {
            $this->forge->dropKey('pengiriman', 'idx_pengiriman_tanggal');
            $this->forge->dropKey('pengiriman', 'idx_pengiriman_pelanggan');
            $this->forge->dropKey('pengiriman', 'idx_pengiriman_kurir');
            $this->forge->dropKey('pengiriman', 'idx_pengiriman_status');
            $this->forge->dropKey('pengiriman', 'idx_pengiriman_date_status');
        }

        // Detail pengiriman table indexes
        if ($this->db->tableExists('detail_pengiriman')) {
            $this->forge->dropKey('detail_pengiriman', 'idx_detail_pengiriman');
            $this->forge->dropKey('detail_pengiriman', 'idx_detail_barang');
        }

        // Barang table indexes
        if ($this->db->tableExists('barang')) {
            $this->forge->dropKey('barang', 'idx_barang_kategori');
            $this->forge->dropKey('barang', 'idx_barang_nama');
        }

        // Pelanggan table indexes
        if ($this->db->tableExists('pelanggan')) {
            $this->forge->dropKey('pelanggan', 'idx_pelanggan_nama');
        }

        // Kurir table indexes
        if ($this->db->tableExists('kurir')) {
            $this->forge->dropKey('kurir', 'idx_kurir_nama');
        }

        // Kategori table indexes
        if ($this->db->tableExists('kategori')) {
            $this->forge->dropKey('kategori', 'idx_kategori_nama');
        }
    }
}
