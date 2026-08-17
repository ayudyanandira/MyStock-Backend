<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->enum('jenis_transaksi', ['MASUK', 'KELUAR', 'OPNAME']);
            $table->decimal('jumlah', 12, 2);
            $table->decimal('stok_awal', 12, 2);
            $table->decimal('stok_akhir', 12, 2);
            $table->string('referensi')->nullable(); // Misal: "PO-0012" atau "OUT-004"
            $table->string('keterangan')->nullable(); // Misal: "Penerimaan dari Supplier A" / "Menu Selasa"
            $table->timestamps(); // Mengcover created_at (Tanggal & Jam presisi)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_mutasis');
    }
};