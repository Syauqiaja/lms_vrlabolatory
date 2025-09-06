<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'description',
        'passing_grade',
        'related_activity_id',
    ];

    public function activity(){
        return $this->belongsTo(Activity::class, 'related_activity_id');
    }

    public function quizQuestions(){
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }

    public function userQuizResults(){
        return $this->hasMany(UserQuizResult::class, 'quiz_id');
    }
}
