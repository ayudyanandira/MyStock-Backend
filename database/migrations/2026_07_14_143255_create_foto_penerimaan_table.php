<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foto_penerimaan', function (Blueprint $table) {

            $table->id();

            $table->foreignId('penerimaan_id')
                ->constrained('penerimaan')
                ->cascadeOnDelete();

            $table->string('nama_file');

            $table->string('path_file');

            $table->string('mime_type',50);

            $table->unsignedBigInteger('ukuran_file');

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_penerimaan');
    }
};