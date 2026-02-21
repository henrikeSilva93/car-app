<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::Create([
            'name' => 'Aquila Henrique da Silva',
            'email' => 'aquila.henrique@outlook.com',
            'password' => bcrypt('123456'),
        ]);
    }
}
