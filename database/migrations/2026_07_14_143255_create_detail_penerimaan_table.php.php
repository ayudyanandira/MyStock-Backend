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
            $table->decimal('jumlah_diterima', 12, 2);
            $table->decimal('selisih', 12, 2);
            $table->string('status', 10);
            $table->string('kondisi', 100);
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penerimaan');
    }
};
