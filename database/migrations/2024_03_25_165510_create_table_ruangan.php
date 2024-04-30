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
        Schema::create('ruangan', function (Blueprint $table) {
            $table->bigIncrements('RuanganID');
            $table->string('Nama_ruangan');
            $table->enum('Kapasitas', ['1-4 Orang', '5-10 Orang', '11-20 Orang', '21-40 Orang']);
            $table->enum('Lokasi', ['Lab FTI 2', 'Lab FTI 3', 'Fakultas', 'Lab FTI 4']);
            $table->enum('Kategori', ['Ruang Diskusi/Rapat', 'Ruang Perkuliahan', 'Ruang Bebas']);
            $table->longText('Fasilitas');
            $table->binary('Foto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};
