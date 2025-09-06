<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function destroy(Quiz $quiz){
        $quiz->delete();
        return redirect(route('quiz'));
    }
}
