<?php

declare(strict_types=1);

namespace Tests\Feature\Actions;

use App\Actions\ListQuestions;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListQuestionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_no_questions(): void
    {
        $this->assertCount(0, Question::all());

        $action = new ListQuestions();
        $questions = $action->handle();

        $this->assertInstanceOf(Collection::class, $questions);
        $this->assertCount(0, $questions);
    }

    public function test_success_single_question(): void
    {
        Question::factory()
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

        $action = new ListQuestions();
        $questions = $action->handle();

        $this->assertInstanceOf(Collection::class, $questions);
        $this->assertCount(1, $questions);

        $this->assertIsInt($questions[0]->id);
        $this->assertSame('radio', $questions[0]->type);
        $this->assertSame('Are you happy?', $questions[0]->name);

        $this->assertTrue($questions[0]->relationLoaded('options'));
        $this->assertCount(1, $questions[0]->options);
        $this->assertSame('Yes', $questions[0]->options[0]->value);
        $this->assertSame(0, $questions[0]->options[0]->order);
    }

    public function test_success_multiple_questions(): void
    {
        $radioQuestion = Question::factory()
            ->has(QuestionOption::factory(), 'options')
            ->create([
                'type' => 'radio',
                'name' => 'Are you happy?',
            ]);

        $checkboxQuestion = Question::factory()
            ->has(QuestionOption::factory()->state([
                'value' => 'Spain',
                'order' => null,
            ]), 'options')
            ->has(QuestionOption::factory()->state([
                'value' => 'France',
                'order' => 0,
            ]), 'options')
            ->create([
                'type' => 'checkbox',
                'name' => 'What countries have you visited?',
            ]);

        $action = new ListQuestions();
        $questions = $action->handle();
        $this->assertInstanceOf(Collection::class, $questions);
        $this->assertCount(2, $questions);

        $this->assertIsInt($questions[0]->id);
        $this->assertSame('radio', $questions[0]->type);
        $this->assertSame('Are you happy?', $questions[0]->name);
        $this->assertTrue($questions[0]->relationLoaded('options'));

        $this->assertIsInt($questions[1]->id);
        $this->assertSame('checkbox', $questions[1]->type);
        $this->assertSame('What countries have you visited?', $questions[1]->name);

        $this->assertCount(2, $questions[1]->options);
        $this->assertSame('Spain', $questions[1]->options[0]->value);
        $this->assertSame(null, $questions[1]->options[0]->order);

        $this->assertSame('France', $questions[1]->options[1]->value);
        $this->assertSame(0, $questions[1]->options[1]->order);
    }

}
