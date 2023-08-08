<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = ['student','teacher'];
        return [
            'user_id' => User::get('id')->random()->id,
            'type' => $type[array_rand($type,1)],
            'status' => 1,
            'message' => $this->faker->realText(250),
        ];
    }
}
