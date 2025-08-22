<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CITestSeeder extends Seeder
{
    public function run()
    {
        $this->call('Tests\Support\Database\Seeds\UserTestSeeder');
        $this->call('Tests\Support\Database\Seeds\KategoriTestSeeder');
        $this->call('Tests\Support\Database\Seeds\BarangTestSeeder');
        $this->call('Tests\Support\Database\Seeds\PelangganTestSeeder');
        $this->call('Tests\Support\Database\Seeds\KurirTestSeeder');
        $this->call('Tests\Support\Database\Seeds\PengirimanTestSeeder');
    }
}