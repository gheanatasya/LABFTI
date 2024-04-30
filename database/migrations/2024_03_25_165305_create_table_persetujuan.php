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
        Schema::create('persetujuan', function (Blueprint $table) {
            $table->bigIncrements('PersetujuanID');
            $table->unsignedBigInteger('PeminjamanID');
            $table->boolean('Dekan_Approve');
            $table->boolean('WD2_Approve');
            $table->boolean('WD3_Approve');
            $table->boolean('Kepala_Approve');
            $table->boolean('Koordinator_Approve');
            $table->boolean('Petugas_Approve');

            $table->foreign('PeminjamanID')->references('PeminjamanID')->on('peminjaman');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persetujuan');
    }
};
