<?php

namespace Database\Factories;

use App\Models\SearchingHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SearchingHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = SearchingHistory::class;

    public function definition()
    {
        return [
            'user_id' => User::get()->random()->id,
            'question' => $this->faker->realText(50),
        ];
    }
}
