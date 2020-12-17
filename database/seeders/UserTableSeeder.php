<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usernames = ['user1', 'user2', 'user3'];
        $password = Hash::make('password');
        
        foreach ($usernames as $username) {
            $user = User::create([
                'avatar' => null,
                'username' => $username,
                'email' => "{$username}@example.com",
                'password' => $password,
            ]);
        }
    }
}
