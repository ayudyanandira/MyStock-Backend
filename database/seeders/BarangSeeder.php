<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Satuan;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        Barang::truncate();

        Barang::create([

            'nama_barang'=>'Beras Premium',

            'kategori_id'=>Kategori::first()->id,

            'satuan_id'=>Satuan::where('nama','Kg')->first()->id,

            'stok'=>250,

            'stok_minimum'=>50,

            'is_active'=>true
        ]);

        Barang::create([

            'nama_barang'=>'Telur Ayam',

            'kategori_id'=>Kategori::where('nama','Protein Hewani')->first()->id,

            'satuan_id'=>Satuan::where('nama','Butir')->first()->id,

            'stok'=>800,

            'stok_minimum'=>150,

            'is_active'=>true
        ]);

        Barang::create([

            'nama_barang'=>'Minyak Goreng',

            'kategori_id'=>Kategori::where('nama','Lainnya')->first()->id,

            'satuan_id'=>Satuan::where('nama','Liter')->first()->id,

            'stok'=>100,

            'stok_minimum'=>20,

            'is_active'=>true
        ]);
    }
}