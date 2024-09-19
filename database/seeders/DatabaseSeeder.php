<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $admin = Admin::create([
            'email'=>'admin@admin.com',
            'password'=>Hash::make(123456789)
        ]);
        
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);

        $admin->assignRole($role);

    }
}
