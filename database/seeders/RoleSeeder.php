<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Rolleri oluştur
        $superAdminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $personelRole = Role::create(['name' => 'Personel', 'guard_name' => 'web']);

    }
}
