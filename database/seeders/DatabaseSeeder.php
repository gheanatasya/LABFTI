<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Dokumen;
use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserTableSeeder::class);
        $this->call(DokumenSeeder::class);
        $this->call(PeminjamanSeeder::class);
        $this->call(PeminjamanAlatSeeder::class);
        $this->call(PeminjamanRuanganSeeder::class);

    }
}
