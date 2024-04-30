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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->bigIncrements('PeminjamanID');
            $table->unsignedBigInteger('PeminjamID');
            $table->unsignedBigInteger('DokumenID');
            $table->datetime('Tanggal_pinjam');
            $table->longText('Keterangan');
            $table->boolean('Is_Personal');
            $table->boolean('Is_Organisation');
            $table->boolean('Is_Eksternal');
            $table->timestamps();

            $table->foreign('PeminjamID')->references('PeminjamID')->on('peminjam');
            $table->foreign('DokumenID')->references('DokumenID')->on('dokumen');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
