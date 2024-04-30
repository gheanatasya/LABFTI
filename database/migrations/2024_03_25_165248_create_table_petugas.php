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
        Schema::create('petugas', function (Blueprint $table) {
            $table->bigIncrements('PetugasID');
            $table->uuid('UserID');
            $table->string('Nama', 50);
            $table->date('Tgl Bekerja');
            $table->date('Tgl Berhenti');
            $table->binary('Foto');
            $table->timestamps();
        
            $table->foreign('UserID')->references('UserID')->on('user');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petugas');
    }
};
