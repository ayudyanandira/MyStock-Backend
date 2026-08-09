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
        Schema::create('supplier', function (Blueprint $table) {

            $table->id();

            $table->string('nama_supplier', 150);

            $table->text('alamat')->nullable();

            $table->string('no_telepon', 20)->nullable();

            $table->string('email', 100)->nullable();
            $table->string('jenis_barang', 150)->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};
