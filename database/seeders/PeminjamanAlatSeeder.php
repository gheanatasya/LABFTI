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
            $detailalatID = DB::table('detail_alat')->pluck('DetailAlatID');
            $waktuPakai = $faker->time('H:i:s');
            $waktuPakaiDateTime = \DateTime::createFromFormat('H:i:s', $waktuPakai);
            $waktuPengambilanDateTime = clone $waktuPakaiDateTime;
            $waktuPengambilanDateTime->modify('-1 hour');

            $waktuPakaiFormatted = $waktuPakaiDateTime->format('H:i');
            $waktuPengambilanFormatted = $waktuPengambilanDateTime->format('H:i');

            for ($i = 1; $i <= 50; $i++) {
                DB::table('peminjaman_alat_bridge')->insert([
                    'PeminjamanID' => $faker->randomElement($peminjamanID),
                    'DetailAlatID' => $faker->randomElement($detailalatID),
                    'Tanggal_pakai_awal' => $faker->date('d-m-Y'),
                    'Tanggal_pakai_akhir' => $faker->date('d-m-Y'),
                    'Waktu_pakai' => $faker->time($waktuPakaiFormatted),
                    'Waktu_selesai' => $faker->time('H:i:s'),
                    'Waktu_pengambilan' => $faker->time($waktuPengambilanFormatted),
                    'Tanggal_pengembalian' => $faker->date('d-m-Y'),
                    'Waktu_pengembalian' => $faker->time('H:i:s'),
                    'Jumlah_pinjam' => $faker->numberBetween(1, 2)
                ]);
            }
        }
    }
}
