<?php

namespace Database\Factories;

use App\Models\FotoPenerimaan;
use App\Models\Penerimaan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FotoPenerimaanFactory extends Factory
{
    protected $model = FotoPenerimaan::class;

    public function definition(): array
    {
        return [

            'penerimaan_id' => Penerimaan::factory(),

            'nama_file' => 'default.png',

            'path_file' => 'penerimaan/default.png',

            'mime_type' => 'image/png',

            'ukuran_file' => 1,

            'uploaded_by' => User::factory(),
        ];
    }
}
