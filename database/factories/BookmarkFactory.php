<?php

namespace Database\Factories;

use App\Models\Bookmark;
use App\Models\CMS\Answer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookmarkFactory extends Factory
{

    protected $model = Bookmark::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
            'user_id' => User::query()->get()->random()->id,
            'index_id' => Answer::query()->whereNotNull('index_id')->get()->random()->index_id,
        ];
    }
}
