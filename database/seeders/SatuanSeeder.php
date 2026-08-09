<?php

namespace Database\Seeders;

use App\Models\Satuan;
use Illuminate\Database\Seeder;

class SatuanSeeder extends Seeder
{
    public function run(): void
    {
        Satuan::truncate();

        $data = [

            ['nama' => 'Kg'],
            ['nama' => 'Gram'],
            ['nama' => 'Liter'],
            ['nama' => 'Ml'],
            ['nama' => 'Butir'],
            ['nama' => 'Ikat'],
            ['nama' => 'Pcs'],
            ['nama' => 'Pack'],
            ['nama' => 'Karung'],
            ['nama' => 'Dus'],

        ];

        Satuan::insert($data);
    }
}