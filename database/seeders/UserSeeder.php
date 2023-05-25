<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\FileManagementService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'          => 'Admin',
            'email'         => 'admin@mail.com',
            'phone'         => '083830388003',
            'profile_pict'  => FileManagementService::uploadImage(file_get_contents('https://api.dicebear.com/6.x/personas/png?seed=admin'), 'profile'),
            'password'      => bcrypt('admin123'),
            'role'          => 'ADMIN'
        ]);

        User::create([
            'name'          => 'Resto 1',
            'email'         => 'resto1@mail.com',
            'phone'         => '083830388003',
            'profile_pict'  => FileManagementService::uploadImage(file_get_contents('https://api.dicebear.com/6.x/personas/png?seed=resto1'), 'profile'),
            'password'      => bcrypt('admin123'),
            'role'          => 'RESTO'
        ]);

        User::create([
            'name'          => 'Resto 2',
            'email'         => 'resto2@mail.com',
            'phone'         => '083830388003',
            'profile_pict'  => FileManagementService::uploadImage(file_get_contents('https://api.dicebear.com/6.x/personas/png?seed=resto2'), 'profile'),
            'password'      => bcrypt('admin123'),
            'role'          => 'RESTO'
        ]);

        User::create([
            'name'          => 'Investor 1',
            'email'         => 'investor1@mail.com',
            'phone'         => '083830388003',
            'profile_pict'  => FileManagementService::uploadImage(file_get_contents('https://api.dicebear.com/6.x/personas/png?seed=investor1'), 'profile'),
            'password'      => bcrypt('admin123'),
            'role'          => 'INVESTOR'
        ]);

        User::create([
            'name'          => 'Investor 2',
            'email'         => 'investor2@mail.com',
            'phone'         => '083830388003',
            'profile_pict'  => FileManagementService::uploadImage(file_get_contents('https://api.dicebear.com/6.x/personas/png?seed=investor2'), 'profile'),
            'password'      => bcrypt('admin123'),
            'role'          => 'INVESTOR'
        ]);

        for ($i = 1; $i <= 10 ; $i++) { 
            User::create([
                'name'          => "Enterprise $i",
                'email'         => "enterprise$i@mail.com",
                'phone'         => '083830388003',
                'profile_pict'  => FileManagementService::uploadImage(file_get_contents("https://api.dicebear.com/6.x/personas/png?seed=enterprise$i"), 'profile'),
                'password'      => bcrypt('admin123'),
                'role'          => 'ENTERPRISE'
            ]);
        }
    }
}
