<?php

namespace Database\Factories;

use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionOption>
 */
#[UseModel(QuestionOption::class)]
class QuestionOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value' => fake()->word(),
            'order' => fake()->numberBetween(1, 100),
        ];
    }
}
