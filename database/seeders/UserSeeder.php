<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        $superAdmin = User::create([
            'name'      => 'Thy Chanthoeurn',
            'email'     => 'ctkh271102@gmail.com',
            'password'  => bcrypt('12345678'),
            'phone'     => '097 83 16 990',
            'address'   => 'Phnom Penh, Cambodia',
        ]);

        // Assign super admin role
        $superAdmin->assignRole(RolesEnum::SUPER_ADMIN->value);
    }
}
