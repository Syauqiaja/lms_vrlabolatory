<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id',
        'order',
        'question',
        'answer_a',
        'answer_b',
        'answer_c',
        'answer_d',
    ];
}
