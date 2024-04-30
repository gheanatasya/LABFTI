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
        Schema::create('user', function (Blueprint $table) {
            $table->uuid('UserID')->primary();
            $table->bigInteger('NIM_NIDN');
            $table->string('Email', 50);
            $table->string('Password', 50);
            $table->enum('User_role', ['Petugas', 'Kepala Lab', 'Koordinator Lab', 'Mahasiswa', 'Dosen', 'Wakil Dekan 2', 'Wakil Dekan 3', 'Dekan', 'Staff']);
            $table->integer('User_priority');
            $table->timestamps();        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
