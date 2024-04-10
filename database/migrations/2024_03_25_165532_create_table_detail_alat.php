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
        Schema::create('detail_alat', function (Blueprint $table) {
            $table->bigIncrements('DetailAlatID');
            $table->unsignedBigInteger('AlatID');
            $table->string('Nama_alat', 50);
            $table->enum('Status_Kebergunaan', ['Dapat Digunakan', 'Rusak']);
            $table->enum('Status_Peminjaman', ['Dipinjam', 'Tersedia']);
            $table->binary('Foto');
            $table->timestamps();

            $table->foreign('AlatID')->references('AlatID')->on('alat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_alat');
    }
};
