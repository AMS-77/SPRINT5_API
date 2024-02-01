<?php

namespace Database\Factories;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Game::class;

    public function definition()
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'dice1' => $this->faker->numberBetween(1, 6),
            'dice2' => $this->faker->numberBetween(1, 6),
            'game_won' => $this->faker->boolean,
        ];
    }
}
