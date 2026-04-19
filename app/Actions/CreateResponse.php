<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Response;
use Closure;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Fluent;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Illuminate\Database\Eloquent\Collection;

class CreateResponse
{
    public function handle(array $data)
    {
        /** @var Collection<int, Question> $questions */
        $questions = Question::query()->with('options')->get()->keyBy('id');
        
        $answerMatchesQuestionRule = function (string $attribute, mixed $value, Closure $fail) use ($questions) {
            $questionId = $value['question_id'] ?? null;
            $answerValue = $value['value'] ?? null;

            if ($questions->has($questionId) && is_array($answerValue)) {
                $validOptions = $questions[$questionId]->options->pluck('value')->toArray();

                if (!empty(array_diff($answerValue, $validOptions))) {
                    $fail("{$attribute}.value does not match the options for question_id {$questionId}.");
                }
            }
        };

        $rules = [
            'answers.*' => [$answerMatchesQuestionRule],
            'answers.*.question_id' => ['required', 'distinct', 'exists:questions,id'],
            'answers.*.value' => ['required', 'array', 'list'],
            'answers.*.value.*' => ['string'],
        ];

        $validator = ValidatorFacade::make(['answers' => $data], $rules);

        // prevent radio question from receiving more than one answer
        $validator->sometimes('answers.*.value', 'size:1', function (Fluent $input, Fluent $item) use ($questions) {
            return $questions->has($item->question_id) 
                && $questions[$item->question_id]->type === Question::TYPE_RADIO 
                && is_array($item->value)
                && count($item->value) > 1;
        });

        $validator->after(function (Validator $validator) use ($data) {
            if (empty($data)) {
                $validator->errors()->add('', 'At least one answer is required.');
            }
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $response = new Response();
        $response->save();

        foreach ($data as $item) {
            foreach ($item['value'] as $value) {
                $answer = new Answer();
                $answer->response_id = $response->id;
                $answer->question_id = $item['question_id'];
                $answer->value = $value;
                $answer->save();
            }
        }

        return $response;
    }
}
