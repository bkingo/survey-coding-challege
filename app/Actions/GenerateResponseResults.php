<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Facades\DB;
use stdClass;

class GenerateResponseResults
{
    public function handle(): array
    {
        /** @var array<int, stdClass> $data */
        $data = DB::select(
            <<<'SQL'
            SELECT
                frequencies.question_id,
                frequencies.question_name,
                frequencies.answer_value,
                frequencies.answer_freq AS answer_value_count,
                max_frequencies.max_freq AS max_answer_value_count
            FROM (
                SELECT
                    q.id AS question_id,
                    q.name AS question_name,
                    a.value AS answer_value,
                    COUNT(*) AS answer_freq
                FROM questions q
                LEFT JOIN answers AS a ON a.question_id = q.id
                GROUP BY q.id, q.name, a.value
            ) AS frequencies
            INNER JOIN (
                SELECT
                    question_id,
                    MAX(answer_freq) AS max_freq
                FROM (
                    SELECT
                        q.id AS question_id,
                        q.name AS question_name,
                        a.value AS answer_value,
                        COUNT(*) AS answer_freq
                    FROM questions q
                    LEFT JOIN answers AS a ON a.question_id = q.id
                    GROUP BY q.id, q.name, a.value
                ) AS frequencies2
                GROUP BY question_id
            ) AS max_frequencies
            ON max_frequencies.question_id = frequencies.question_id
            AND frequencies.answer_freq = max_frequencies.max_freq
            SQL
        );

        $results = [];

        foreach ($data as $item) {
            if (!isset($results[$item->question_id])) {
                $results[$item->question_id] = [
                    'question_id' => $item->question_id,
                    'question_name' => $item->question_name,
                    'answer_value' => [$item->answer_value],
                    'answer_value_count' => $item->answer_value_count,
                    'max_answer_value_count' => $item->max_answer_value_count,
                ];
            } else {
                $results[$item->question_id]['answer_value'][] = $item->answer_value;
            }
        }

        return array_values($results);
    }
}
