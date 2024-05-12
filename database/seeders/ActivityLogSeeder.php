<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $statuspeminjamanID = DB::table('status_peminjaman')->pluck('Status_PeminjamanID');
        for ($i = 1; $i <= 50; $i++) {
            DB::table('activity_log')->insert([
                'Status_PeminjamanID' => $faker->randomElement($statuspeminjamanID),
            ]);
        }
    }
}
