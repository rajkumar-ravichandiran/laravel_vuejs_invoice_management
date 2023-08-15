<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class CreateDefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@invoice.app',
            'password' => Hash::make('Invoice@PassWord'),
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'email_verified_at' => now()
        ]);

        $adminrole = Role::findByName('admin');
        $admin->assignRole($adminrole);
    }
}

