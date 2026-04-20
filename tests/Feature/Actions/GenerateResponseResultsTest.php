<?php

declare(strict_types=1);

namespace Tests\Feature\Actions;

use App\Actions\GenerateResponseResults;
use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenerateResponseResultsTest extends TestCase
{
    use RefreshDatabase;

    /** How old are you? */
    private Question $question1;

    /** Are you happy? */
    private Question $question2;

    /** What countries have you visited? */
    private Question $question3;

    /** What is your favorite sport? */
    private Question $question4;
    
    /** What programming languages do you know? */
    private Question $question5;

    protected function setUp(): void
    {
        parent::setUp();

        $this->question1 = Question::factory()
            ->has(QuestionOption::factory()->state(['value' => 'Less than 18', 'order' => 0]), 'options')
            ->has(QuestionOption::factory()->state(['value' => '18-99', 'order' => 1]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'More than 99', 'order' => 2]), 'options')
            ->create([
                'type' => 'radio',
                'name' => 'How old are you?',
            ]);

        $this->question2 = Question::factory()
            ->has(QuestionOption::factory()->state(['value' => 'Yes', 'order' => 0]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'No', 'order' => 1]), 'options')
            ->create([
                'type' => 'radio',
                'name' => 'Are you happy?',
            ]);

        $this->question3 = Question::factory()
            ->has(QuestionOption::factory()->state(['value' => 'Spain', 'order' => 0]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'France', 'order' => 1]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'Italy', 'order' => 2]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'England', 'order' => 3]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'Portugal', 'order' => 4]), 'options')
            ->create([
                'type' => 'checkbox',
                'name' => 'What countries have you visited?',
            ]);

        $this->question4 = Question::factory()
            ->has(QuestionOption::factory()->state(['value' => 'Football', 'order' => 0]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'Basketball', 'order' => 1]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'Soccer', 'order' => 2]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'Volleyball', 'order' => 3]), 'options')
            ->create([
                'type' => 'radio',
                'name' => 'What is your favorite sport?',
            ]);

        $this->question5 = Question::factory()
            ->has(QuestionOption::factory()->state(['value' => 'PHP', 'order' => 0]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'Ruby', 'order' => 1]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'JavaScript', 'order' => 2]), 'options')
            ->has(QuestionOption::factory()->state(['value' => 'Python', 'order' => 3]), 'options')
            ->create([
                'type' => 'checkbox',
                'name' => 'What programming languages do you know?',
            ]);
    }

    /**
     * GET /responses/results
     *
     * SELECT question_id, question_name, answer_value, COUNT(*) as answer_value_count
     * GROUP BY (question_id, question_name answer_value)
     *
     * question_id question_name answer_value answer_value_count
     * 1            How old?     18-99          10
     * 1            How old?     Less than 18    5
     * 2            Happpy?      Yes             10
     * 2            Happpy?      No              5
     * 3            Countries?   France          10
     * 3            Countries?   Italy           3
     * 3            Countries?   England         0?
     * 3            Countries?   Portugal        5
     * 4            Sport?       Soccer          10
     * 4            Sport?       Football        5
     * 4            Sport?       Basketball      5
     *
     * [
     *     {
     *         question_id: 1,
     *         question_name: 'How old are you?',
     *         answer_value: '18-99',
     *         answer_value_count: 10,
     *         max_answer_value_count: 10,
     *     },
     * ]
     *
     * $action = new CreateResponseReport();
     * $report = $action->handle();
     *
     * report is an array of objects with the following properties:
     * - question_id
     * - question_name
     * - answer_value
     * - answer_value_count
     * - max_answer_value_count
     *
     * scenarios:
     * - no responses
     * - one response
     * - multiple responses
     * - multiple responses with the same answer value
     * - multiple responses with the same answer value for different questions
     * - multiple responses with the same answer value for the same question
     *
     * all questions answered
     * no responses yet
     * some questions answered
     *
     * multiple responses
     * no responses
     * 
     * how to handle ties?
     */

    // public function test_success_no_responses(): void
    // {
    //     $this->assertCount(0, Response::all());

    //     $action = new CreateResponseReport();
    //     $report = $action->handle();

    //     $this->assertInstanceOf(Collection::class, $report);
    //     $this->assertCount(0, $report);
    // }

    public function test_success_all_questions_answered(): void
    {
        /**
         * add answers to all questions
         * generate the results
         * verify that all questions are there with the expected values as array of objects
         * verify max_answer_value_count is equal to answer_value_count
         */
        $response = Response::factory()
            ->has(Answer::factory()->state(['question_id' => $this->question1->id, 'value' => '18-99']), 'answers')
            ->has(Answer::factory()->state(['question_id' => $this->question2->id, 'value' => 'Yes']), 'answers')
            ->has(Answer::factory()->state(['question_id' => $this->question3->id, 'value' => 'France']), 'answers')
            ->has(Answer::factory()->state(['question_id' => $this->question4->id, 'value' => 'Soccer']), 'answers')
            ->has(Answer::factory()->state(['question_id' => $this->question5->id, 'value' => 'Ruby']), 'answers')
            ->has(Answer::factory()->state(['question_id' => $this->question5->id, 'value' => 'PHP']), 'answers')
            ->create();

        $action = new GenerateResponseResults();
        $results = $action->handle();

        $this->assertIsArray($results);
        $this->assertCount(5, $results);

        $this->assertSame($this->question1->id, $results[0]['question_id']);
        $this->assertSame($this->question1->name, $results[0]['question_name']);
        $this->assertSame(['18-99'], $results[0]['answer_value']);
        $this->assertSame(1, $results[0]['answer_value_count']);
        $this->assertSame(1, $results[0]['max_answer_value_count']);

        $this->assertSame($this->question2->id, $results[1]['question_id']);
        $this->assertSame($this->question2->name, $results[1]['question_name']);
        $this->assertSame(['Yes'], $results[1]['answer_value']);
        $this->assertSame(1, $results[1]['answer_value_count']);
        $this->assertSame(1, $results[1]['max_answer_value_count']);

        $this->assertSame($this->question3->id, $results[2]['question_id']);
        $this->assertSame($this->question3->name, $results[2]['question_name']);
        $this->assertSame(['France'], $results[2]['answer_value']);
        $this->assertSame(1, $results[2]['answer_value_count']);
        $this->assertSame(1, $results[2]['max_answer_value_count']);

        $this->assertSame($this->question4->id, $results[3]['question_id']);
        $this->assertSame($this->question4->name, $results[3]['question_name']);
        $this->assertSame(['Soccer'], $results[3]['answer_value']);
        $this->assertSame(1, $results[3]['answer_value_count']);
        $this->assertSame(1, $results[3]['max_answer_value_count']);

        $this->assertSame($this->question5->id, $results[4]['question_id']);
        $this->assertSame($this->question5->name, $results[4]['question_name']);
        $this->assertSame(['PHP', 'Ruby'], $results[4]['answer_value']); // answers are tied
        $this->assertSame(1, $results[4]['answer_value_count']);
        $this->assertSame(1, $results[4]['max_answer_value_count']);
    }
}
