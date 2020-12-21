<?php

namespace Database\Seeders;

use App\Models\Choice;
use App\Models\Packet;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

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
        $this->call(UserTableSeeder::class);
        
        Packet::factory(40)
            ->has(Quiz::factory()
                            ->has(Choice::factory()->count(4))
                            ->count(rand(0, 1) === 1 ? 40 : 25))
            ->create();

        $this->call(RightChoiceSeeder::class);
    }
}
