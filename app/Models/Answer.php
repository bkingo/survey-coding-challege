<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AnswerFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(AnswerFactory::class)]
class Answer extends Model
{
    use HasFactory;
}
