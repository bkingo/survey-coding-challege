<?php

namespace Database\Factories;

use App\Models\Model;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
#[UseModel(Question::class)]
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence() . '?',
            'type' => fake()->randomElement(['radio', 'checkbox']),
        ];
    }
}
