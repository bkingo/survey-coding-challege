<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);


        $questions = [
            [
                'type' => 'radio',
                'name' => 'How old are you?',
                'options' => [
                    ['value' => 'Less than 18', 'order' => 1],
                    ['value' => '18-99', 'order' => 2],
                    ['value' => 'More than 99', 'order' => 3],
                ],
            ],
            [
                'type' => 'radio',
                'name' => 'Are you happy?',
                'options' => [
                    ['value' => 'Yes', 'order' => 1],
                    ['value' => 'No', 'order' => 2],
                ],
            ],
            [
                'type' => 'checkbox',
                'name' => 'What countries have you visited?',
                'options' => [
                    ['value' => 'Spain', 'order' => 1],
                    ['value' => 'France', 'order' => 2],
                    ['value' => 'Italy', 'order' => 3],
                    ['value' => 'England', 'order' => 4],
                    ['value' => 'Portugal', 'order' => 5],
                ],
            ],
            [
                'type' => 'radio',
                'name' => 'What is your favorite sport?',
                'options' => [
                    ['value' => 'Football', 'order' => 1],
                    ['value' => 'Basketball', 'order' => 2],
                    ['value' => 'Soccer', 'order' => 3],
                    ['value' => 'Volleyball', 'order' => 4],
                ],
            ],
            [
                'type' => 'checkbox',
                'name' => 'What programming languages do you know?',
                'options' => [
                    ['value' => 'PHP', 'order' => 1],
                    ['value' => 'Ruby', 'order' => 2],
                    ['value' => 'JavaScript', 'order' => 3],
                    ['value' => 'Python', 'order' => 4],
                ],
            ],
        ];

        foreach ($questions as $question) {
            $questionFactory = Question::factory();

            foreach ($question['options'] as $option) {
                $questionFactory = $questionFactory->has(QuestionOption::factory()->count(1)->state($option), 'options');
            }

            $questionFactory->create([
                'type' => $question['type'],
                'name' => $question['name'],
            ]);
        }

    }
}
