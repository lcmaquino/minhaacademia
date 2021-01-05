<?php

use App\User;
use App\Role;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_admin = Role::where('name', 'admin')->first();

        User::create([
            'name' => 'Admin',
            'email' => 'admin@localhost',
            'password' => Hash::make('admin'),
            'role' => $role_admin->id,
        ]);
    }
}
