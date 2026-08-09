<?php

namespace Database\Factories;

use App\Models\Penerimaan;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenerimaanFactory extends Factory
{
    protected $model = Penerimaan::class;

    public function definition(): array
    {
        return [

            'nomor_transaksi' => sprintf(
                'TRX-IN-%s-%04d',
                now()->format('Ymd'),
                fake()->unique()->numberBetween(1, 9999)
            ),

            'supplier_id' => Supplier::factory(),

            'tanggal' => fake()->date(),

        ];
    }
}
