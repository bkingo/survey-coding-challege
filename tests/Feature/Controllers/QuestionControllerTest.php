<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuestionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_success(): void
    {
        $question = Question::factory()
            ->has(
                QuestionOption::factory()
                    ->state([
                        'value' => 'Yes',
                        'order' => 0,
                    ]),
                'options',
            )
            ->create([
                'type' => 'radio',
                'name' => 'Are you happy?',
            ]);


        $response = $this->getJson('/api/questions');
        $response->assertStatus(200);
        $response->assertJson([
            [
                'id' => $question->id,
                'type' => 'radio',
                'name' => 'Are you happy?',
                'options' => [
                    [
                        'id' => $question->options[0]->id,
                        'question_id' => $question->id,
                        'value' => 'Yes',
                        'order' => 0,
                    ],
                ],
            ],
        ]);
    }
}
