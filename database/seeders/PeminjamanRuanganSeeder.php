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
        $dokumenID = DB::table('dokumen')->pluck('DokumenID');

        for ($i = 1; $i <= 50; $i++) {
            DB::table('peminjaman_ruangan_bridge')->insert([
                'PeminjamanID' => $faker->randomElement($peminjamanID),
                'RuanganID' => $faker->randomElement($ruanganID),
                'Tanggal_pakai_awal' => $faker->date(),
                'Tanggal_pakai_akhir' => $faker->date(),
                'Keterangan' => $faker->text(100),
                'Is_Personal' => $faker->boolean(60),
                'Is_Organisation' => $faker->boolean(30),
                'Is_Eksternal' => $faker->boolean(20),
                'DokumenID' => $faker->randomElement($dokumenID)
            ]);
        }
    }
}
