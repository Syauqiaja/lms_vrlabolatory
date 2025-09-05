<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuizResult extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'note',
    ];
}
