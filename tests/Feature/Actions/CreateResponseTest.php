<?php

namespace Tests\Feature\Actions;

use App\Actions\CreateResponse;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_fails_if_answer_invalid(): void
    {
        $question1 = Question::factory()
            ->has(QuestionOption::factory()->state(['value' => 'Yes','order' => 0]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'No','order' => 1]), 'options')
            ->create([
                'type' => 'radio',
                'name' => 'Are you happy?',
        ]);

        $question2 = Question::factory()
            ->has(QuestionOption::factory()->state(['value' => 'Football','order' => 0]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'Basketball','order' => 1]), 'options')
            ->create([
                'type' => 'checkbox',
                'name' => 'What is your favorite sport?',
        ]);

        $question3 = Question::factory()->create([
            'type' => 'radio',
            'name' => 'Are you tall?'
        ]);

        $data = [
            // 0
            [
                'value' => ['Yes'],
            ],
            // 1
            [
                'question_id' => null,
                'value' => ['Yes'],
            ],
            // 2
            [
                'question_id' => PHP_INT_MAX,
                'value' => 'not array',
            ],
            // 3
            [
                'question_id' => $question3->id,
                'value' => [],
            ],
            // 4
            [
                'question_id' => $question1->id,
                'value' => ['Yes', 2],
            ],
            // 5
            [
                'question_id' => PHP_INT_MAX,
                'value' => ['key' => 'value'],
            ],
            // 6
            [
                'question_id' => $question2->id, 
                'value' => ['Jai alai']
            ],
        ];

        $expectedErrors = [
            'answers.0.question_id' => ['The answers.0.question_id field is required.'],
            'answers.1.question_id' => ['The answers.1.question_id field is required.'],
            'answers.2.question_id' => ['The answers.2.question_id field has a duplicate value.'],
            'answers.2.value' => ['The answers.2.value field must be an array.', 'The answers.2.value field must be a list.'],
            'answers.3.value' => ['The answers.3.value field is required.'],
            'answers.4' => ["answers.4.value does not match the options for question_id {$question1->id}."],
            'answers.4.value' => ['The answers.4.value field must contain 1 items.'],
            'answers.4.value.1' => ['The answers.4.value.1 field must be a string.'],
            'answers.5.question_id' => ['The answers.5.question_id field has a duplicate value.'],
            'answers.5.value' => ['The answers.5.value field must be a list.'],
            'answers.6' => ["answers.6.value does not match the options for question_id {$question2->id}."],
        ];

        $this->assertSame(0, Response::count());

        try {
            $action = new CreateResponse();
            $action->handle($data);
            $this->fail('Expected ValidationException.');
        } catch (ValidationException $e) {
            $this->assertEquals($expectedErrors, $e->errors());
        }

        $this->assertSame(0, Response::count());
    }

    public function test_success_single_answer(): void
    {
        $question = Question::factory()
            ->has(QuestionOption::factory()->state(['value' => 'Yes','order' => 0]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'No','order' => 1]), 'options')
            ->create([
                'type' => 'radio',
                'name' => 'Are you happy?',
            ]);

        $this->assertSame(0, Response::count());

        $data = [
            [
                'question_id' => $question->id,
                'value' => ['Yes'],
            ],
        ];

        $action = new CreateResponse();
        $response = $action->handle($data);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(1, Response::count());

        $this->assertDatabaseHas('answers', [
            'question_id' => $question->id,
            'value' => 'Yes',
        ]);
    }
}
