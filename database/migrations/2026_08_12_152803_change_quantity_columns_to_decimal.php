<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah stok di tabel barang
        Schema::table('barang', function (Blueprint $table) {
            $table->decimal('stok', 10, 2)->default(0)->change();
        });

        // 2. Ubah kolom jumlah di tabel detail_penerimaan
        Schema::table('detail_penerimaan', function (Blueprint $table) {
            $table->decimal('jumlah_pesanan', 10, 2)->default(0)->change();
            $table->decimal('jumlah_diterima', 10, 2)->default(0)->change();
            $table->decimal('selisih', 10, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->integer('stok')->change();
        });

        Schema::table('detail_penerimaan', function (Blueprint $table) {
            $table->integer('jumlah_pesanan')->change();
            $table->integer('jumlah_diterima')->change();
            $table->integer('selisih')->change();
        });
    }
};