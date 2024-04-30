<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'Email' => 'admin@ti.ukdw.ac.id',
            'Password' => bcrypt('password'),
            'NIM_NIDN' => 71200586,
            'User_role' => 'Mahasiswa',
            'User_priority' => 1
        ]);
    }
}
