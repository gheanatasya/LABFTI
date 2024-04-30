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
        Schema::create('peminjaman_alat_bridge', function (Blueprint $table) {
            $table->bigIncrements('Peminjaman_Alat_ID');
            $table->unsignedBigInteger('PeminjamanID');
            $table->unsignedBigInteger('AlatID');
            $table->date('Tanggal_pakai_awal');
            $table->date('Tanggal_pakai_akhir');
            $table->time('Waktu_pakai');
            $table->time('Waktu_selesai');
            $table->time('Waktu_pengambilan');
            $table->date('Tanggal_pengembalian');
            $table->time('Waktu_pengembalian');
            $table->integer('Jumlah_pinjam');
            $table->timestamps();

            $table->foreign('PeminjamanID')->references('PeminjamanID')->on('peminjaman');
            $table->foreign('AlatID')->references('AlatID')->on('alat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_alat_bridge');
    }
};
