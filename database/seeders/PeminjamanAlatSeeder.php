<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;


class PeminjamanAlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { {
            $faker = Faker::create('id_ID');
            $peminjamanID = DB::table('peminjaman')->pluck('PeminjamanID');
            $alatID = DB::table('alat')->pluck('AlatID');
            $dokumenID = DB::table('dokumen')->pluck('DokumenID');

            for ($i = 1; $i <= 50; $i++) {
                DB::table('peminjaman_alat_bridge')->insert([
                    'PeminjamanID' => $faker->randomElement($peminjamanID),
                    'AlatID' => $faker->randomElement($alatID),
                    'Tanggal_pakai_awal' => $faker->date(),
                    'Tanggal_pakai_akhir' => $faker->date(),
                    'Jumlah_pinjam' => $faker->numberBetween(1, 10),
                    'Keterangan' => $faker->text(100),
                    'Is_Personal' => $faker->boolean(60),
                    'Is_Organisation' => $faker->boolean(30),
                    'Is_Eksternal' => $faker->boolean(20),
                    'DokumenID' => $faker->randomElement($dokumenID)
                ]);
            }
        }
    }
}
