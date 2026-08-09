<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {

            $table->id();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('reference_type',30);

            $table->unsignedBigInteger('reference_id');

            $table->decimal('qty_in',12,2)->default(0);

            $table->decimal('qty_out',12,2)->default(0);

            $table->decimal('stock_before',12,2);

            $table->decimal('stock_after',12,2);

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};