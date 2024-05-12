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
        $peminjamanID = DB::table('peminjaman')->pluck('PeminjamanID');
        $statusID = DB::table('status')->pluck('StatusID');
        for ($i = 1; $i <= 50; $i++) {
            DB::table('status_peminjaman')->insert([
                'PeminjamanID' => $faker->randomElement($peminjamanID),
                'StatusID' => $faker->randomElement($statusID),
                'Tanggal_Acc' => $faker->date('d-m-Y'),
            ]);
        }
    }
}
