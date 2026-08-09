<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_stok_opname', function (Blueprint $table) {

            $table->id();

            $table->foreignId('stok_opname_id')
                ->constrained('stok_opname')
                ->cascadeOnDelete();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('stok_sistem', 12, 2);

            $table->decimal('stok_fisik', 12, 2);

            $table->decimal('selisih', 12, 2);

            $table->text('keterangan')->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_stok_opname');
    }
};