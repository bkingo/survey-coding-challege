<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ResponseFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(ResponseFactory::class)]
class Response extends Model
{
    use HasFactory;
    
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
