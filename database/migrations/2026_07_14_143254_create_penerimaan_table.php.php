<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi', 40)->unique(); // Bisa diisi Nomor PO / Nota
            
            $table->foreignId('supplier_id')
                ->constrained('supplier')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('tanggal'); // Tanggal Pembuatan PO
            $table->date('tanggal_terima')->nullable(); // Tanggal Barang Sampai di Gudang
            
            // ➕ TAMBAHKAN STATUS PO / DOKUMEN DI SINI
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan');
    }
};