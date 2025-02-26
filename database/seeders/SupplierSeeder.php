<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_supplier' => 'PT Gadget Malang', 'alamat' => 'Malang'],
            ['nama_supplier' => 'Makanan Sehat', 'alamat' => 'Sidoarjo'],
            ['nama_supplier' => 'Toko Olahraga', 'alamat' => 'Bojonegoro'],
        ];
        DB::table('m_supplier')->insert($data);
    }
}