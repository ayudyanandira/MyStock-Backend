<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {

            $table->id();

            $table->string('kode_barang',20)->unique();

            $table->string('nama_barang',150);

            $table->foreignId('kategori_id')
                ->constrained('kategori')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('satuan_id')
                ->constrained('satuan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->integer('stok')->default(0);

            $table->integer('stok_minimum')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};