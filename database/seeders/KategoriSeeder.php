<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kategori_kode' => 'KTG021', 'kategori_nama' => 'Gadget'],
            ['kategori_kode' => 'KTG022', 'kategori_nama' => 'Makanan'],
            ['kategori_kode' => 'KTG023', 'kategori_nama' => 'Minuman'],
            ['kategori_kode' => 'KTG024', 'kategori_nama' => 'Bola'],
            ['kategori_kode' => 'KTG025', 'kategori_nama' => 'Peralatan Kuliah'],
        ];
        DB::table('m_kategori')->insert($data);
    }
}