<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        Kategori::truncate();

        $data = [

            ['nama'=>'Karbohidrat'],

            ['nama'=>'Protein Hewani'],

            ['nama'=>'Protein Nabati'],

            ['nama'=>'Sayuran'],

            ['nama'=>'Buah'],

            ['nama'=>'Bumbu'],

            ['nama'=>'Lainnya']

        ];

        Kategori::insert($data);
    }
}