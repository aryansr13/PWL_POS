<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada minimal 5 kategori sebelum insert barang
        $kategoriCount = DB::table('m_kategori')->count();
        if ($kategoriCount < 5) {
            return;
        }

        $data = [];
        for ($i = 1; $i <= 15; $i++) {
            $data[] = [
                'barang_kode' => 'BRG00' . $i,
                'barang_nama' => 'Barang ' . $i,
                'kategori_id' => rand(1, $kategoriCount), // Pastikan kategori_id valid
                'harga_beli' => rand(5000, 50000),
                'harga_jual' => rand(6000, 60000),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('m_barang')->insert($data);
    }
}