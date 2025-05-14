<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $user =  \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password')
        ]);

        $user->assignRole('Admin');

    }
}
