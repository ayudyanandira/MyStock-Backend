<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penggunaan', function (Blueprint $table) {

            $table->id();

            $table->foreignId('penggunaan_id')
                ->constrained('penggunaan')
                ->cascadeOnDelete();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('jumlah',12,2);

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penggunaan');
    }
};