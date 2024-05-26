<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PetugasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $UserID = DB::table('user')->pluck('UserID');
        for ($i = 1; $i <= 5; $i++) {
            DB::table('petugas')->insert([
                'UserID' => $faker->randomElement($UserID),
                'Nama' => $faker->text(),
                'Tgl_Bekerja' => $faker->dateTime('d-m-Y H:i:s'),
                'Tgl_Berhenti' => $faker->dateTime('d-m-Y H:i:s'),
                'Foto' => $faker->text(),
            ]);
        }
    }
}
