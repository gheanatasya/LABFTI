<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PersetujuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $peminjamanID = DB::table('peminjaman')->pluck('PeminjamanID');
        for ($i = 1; $i <= 50; $i++) {
            DB::table('persetujuan')->insert([
                'PeminjamanID' => $faker->randomElement($peminjamanID),
                'Dekan_Approve' => $faker->boolean(),
                'WD2_Approve' => $faker->boolean(),
                'WD3_Approve' => $faker->boolean(),
                'Kepala_Approve' => $faker->boolean(),
                'Koordinator_Approve' => $faker->boolean(),
                'Petugas_Approve' => $faker->boolean(),
            ]);
        }
    }
}
