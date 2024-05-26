<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $peminjamID = DB::table('peminjam')->pluck('PeminjamID');
        $dokumenID = DB::table('dokumen')->pluck('DokumenID');

        for($i = 1; $i <= 50; $i++) {
            DB::table('peminjaman')->insert([
                'PeminjamID' => $faker->randomElement($peminjamID),
                'DokumenID' => $faker->randomElement($dokumenID),
                'Tanggal_pinjam' => $faker->dateTime('d-m-Y H:i:s'),
                'Keterangan' => $faker->text(100),
                'Is_Personal' => $faker->boolean(60),
                'Is_Organisation' => $faker->boolean(30),
                'Is_Eksternal' => $faker->boolean(20)
            ]);
        }
    }
}
