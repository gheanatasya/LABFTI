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
        Schema::create('status_peminjaman', function (Blueprint $table) {
            $table->bigIncrements('Status_PeminjamanID');
            $table->unsignedBigInteger('PeminjamanID');
            $table->unsignedBigInteger('StatusID');
            $table->datetime('Tanggal_Acc');
            $table->timestamps();

            $table->foreign('PeminjamanID')->references('PeminjamanID')->on('peminjaman');           
            $table->foreign('StatusID')->references('StatusID')->on('status');  

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_peminjam');
    }
};
