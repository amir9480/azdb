<?php

namespace Database\Factories;

use App\Models\Buisness;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuisnessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Buisness::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'approved'    => true,
            'name'        => $this->faker->word,
            'description' => $this->faker->text,
            'user_id'     => User::inRandomOrder()->first()->id
        ];
    }
}
