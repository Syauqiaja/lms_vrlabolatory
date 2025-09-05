<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuestionAnswer extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_question_id',
        'answer',
        'is_correct',
    ];
}
