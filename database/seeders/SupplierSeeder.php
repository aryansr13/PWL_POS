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
            ['supplier_kode'=>'777','supplier_nama' => 'PT Gadget Malang', 'supplier_alamat' => 'Malang'],
            ['supplier_kode'=>'778','supplier_nama' => 'Makanan Sehat', 'supplier_alamat' => 'Sidoarjo'],
            ['supplier_kode'=>'779','supplier_nama' => 'Toko Olahraga', 'supplier_alamat' => 'Bojonegoro'],
        ];
        DB::table('m_supplier')->insert($data);
    }
}