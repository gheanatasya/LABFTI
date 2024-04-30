<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PeminjamanRuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $peminjamanID = DB::table('peminjaman')->pluck('PeminjamanID');
        $ruanganID = DB::table('ruangan')->pluck('RuanganID');
        for ($i = 1; $i <= 50; $i++) {
            DB::table('peminjaman_ruangan_bridge')->insert([
                'PeminjamanID' => $faker->randomElement($peminjamanID),
                'RuanganID' => $faker->randomElement($ruanganID),
                'Tanggal_pakai_awal' => $faker->date('d-m-Y'),
                'Tanggal_pakai_akhir' => $faker->date('d-m-Y'),
                'Waktu_pakai' => $faker->time('H:i:s'),
                'Waktu_selesai' => $faker->time('H:i:s'),
            ]);
        }
    }
}
