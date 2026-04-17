<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;

class ListQuestions
{
    public function handle(): Collection
    {
        return Question::query()->with('options')->get();
    }
}