<?php

namespace Tests\Feature\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ResponseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_success_single_answer(): void
    {
        $question = Question::factory()
            ->has(QuestionOption::factory()->state(['value' => 'Yes','order' => 0]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'No','order' => 1]), 'options')
            ->create([
                'type' => 'radio',
                'name' => 'Are you happy?',
            ]);

        $this->assertSame(0, Response::count());

        $response = $this->postJson('/api/responses', [
            ['question_id' => $question->id, 'value' => ['Yes']],
        ]);

        $this->assertSame(1, Response::count());
        $responseRecord = Response::first();

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $responseRecord->id,
        ]);
    }
}
