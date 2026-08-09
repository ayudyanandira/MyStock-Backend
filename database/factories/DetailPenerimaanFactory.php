<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\Penerimaan;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailPenerimaanFactory extends Factory
{
    protected $model = DetailPenerimaan::class;

    public function definition(): array
    {
        $jumlah = fake()->numberBetween(10, 100);
        $hargaSatuan = fake()->numberBetween(1_000, 100_000);

        return [

            'penerimaan_id' => Penerimaan::factory(),

            'barang_id' => Barang::factory(),

            'jumlah' => $jumlah,

            'harga_satuan' => $hargaSatuan,

            'subtotal' => $jumlah * $hargaSatuan,

            'catatan' => fake()->optional()->sentence(),
        ];
    }
}
