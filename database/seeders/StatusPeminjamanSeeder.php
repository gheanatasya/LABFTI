<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class StatusPeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $peminjamanruanganID = DB::table('peminjaman_ruangan_bridge')->pluck('Peminjaman_Ruangan_ID');
        $peminjamanalatID = DB::table('peminjaman_alat_bridge')->pluck('Peminjaman_Alat_ID');
        $statusID = DB::table('status')->pluck('StatusID');
        for ($i = 1; $i <= 50; $i++) {
            DB::table('status_peminjaman')->insert([
                'Peminjaman_Ruangan_ID' => $faker->randomElement($peminjamanruanganID),
                'Peminjaman_Alat_ID' => $faker->randomElement($peminjamanalatID),
                'StatusID' => $faker->randomElement($statusID),
                'Tanggal_Acc' => $faker->date('d-m-Y'),
            ]);
        }
    }
}
