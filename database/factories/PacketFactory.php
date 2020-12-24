<?php

namespace Database\Factories;

use App\Models\Packet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PacketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Packet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::select('id')->get();
        $userIds = $users->pluck('id');

        return [
            'user_id' => $userIds->random(),
            'title' => $this->faker->sentence,
        ];
    }
}
