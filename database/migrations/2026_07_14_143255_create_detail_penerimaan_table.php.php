<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penerimaan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('penerimaan_id')
                ->constrained('penerimaan')
                ->cascadeOnDelete();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('jumlah_pesanan', 12, 2);
            // ✏️ Bikin default 0 & nullable agar tidak error saat pembuat PO baru menginput
            $table->decimal('jumlah_diterima', 12, 2)->default(0); 
            $table->decimal('selisih', 12, 2)->default(0);
            $table->string('status', 20)->default('Sesuai'); // Sesuai / Selisih
            $table->string('kondisi', 100)->nullable()->default('Baik');
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penerimaan');
    }
};