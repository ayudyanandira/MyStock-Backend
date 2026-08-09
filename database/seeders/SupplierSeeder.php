<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::truncate();

        Supplier::insert([

            [
                'nama_supplier'=>'CV Sumber Pangan',
                'alamat'=>'Malang',
                'no_telepon'=>'081234567890',
                'email'=>'supplier1@test.com'
            ],

            [
                'nama_supplier'=>'PT Makmur Sentosa',
                'alamat'=>'Surabaya',
                'no_telepon'=>'081111111111',
                'email'=>'supplier2@test.com'
            ],

            [
                'nama_supplier'=>'UD Berkah Jaya',
                'alamat'=>'Batu',
                'no_telepon'=>'082222222222',
                'email'=>'supplier3@test.com'
            ]

        ]);
    }
}