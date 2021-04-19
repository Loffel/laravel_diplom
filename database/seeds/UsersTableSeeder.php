<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@site.local',
            'is_admin' => 1,
            'password' => Hash::make('admin')
        ]);

        User::create([
            'name' => 'user',
            'email' => 'user@site.local',
            'is_admin' => 0,
            'password' => Hash::make('user')
        ]);
    }
}
