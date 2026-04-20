<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ResultsControllerTest extends TestCase
{
    use RefreshDatabase;

    private Question $question;

    protected function setUp(): void
    {
        parent::setUp();

        $this->question = Question::factory()
           ->has(QuestionOption::factory()->state(['value' => 'Less than 18', 'order' => 0]), 'options')
           ->has(QuestionOption::factory()->state(['value' => '18-99', 'order' => 1]), 'options')
           ->has(QuestionOption::factory()->state(['value' => 'More than 99', 'order' => 2]), 'options')
           ->create([
               'type' => 'radio',
               'name' => 'How old are you?',
           ]);

        Response::factory()
            ->has(Answer::factory()->state([
                'question_id' => $this->question->id,
                'value' => 'Less than 18',
            ]), 'answers')
            ->create();
    }

    public function test_index_success(): void
    {
        $response = $this->getJson('/api/results');
        $response->assertStatus(200);
        $response->assertJson([
            [
                'question_id' => $this->question->id,
                'question_name' => 'How old are you?',
                'answer_value' => ['Less than 18'],
                'answer_value_count' => 1,
                'max_answer_value_count' => 1,
            ],
        ]);
    }
}
