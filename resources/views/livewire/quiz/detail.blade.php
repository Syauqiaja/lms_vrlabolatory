<?php
use Livewire\Volt\Component;
use App\Models\Quiz;
use App\Models\UserQuestionAnswer;
use App\Models\UserQuizResult;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $quiz;
    public $questions;
    public $answers = [];
    public $currentQuestionIndex = 0;
    public $showResults = false;
    public $score = 0;
    public $totalQuestions = 0;
    public $hasStarted = false;
    public $hasCompleted = false;
    public $existingResult = null;
    public $timeRemaining = null;
    public $quizDuration = 30; // 30 minutes default
    public $userAttempts;

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz;
        $this->questions = $quiz->quizQuestions()->orderBy('order')->get();
        $this->totalQuestions = $this->questions->count();

        $this->userAttempts = $quiz->userQuizResults()->where('user_id', Auth::id())->get();
        
        // Check if user has already completed this quiz
        $this->existingResult = UserQuizResult::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->first();
            
        if ($this->existingResult) {
            $this->hasCompleted = true;
            $this->score = $this->existingResult->score;
        }
    }
}; ?>

<div class="min-h-screen">
    <x-nav.breadcrumb class="mb-6">
        <x-nav.breadcrumb-item title='Quiz' href="{{ route('admin.quiz') }}" />
        <x-nav.breadcrumb-item title='{{ $quiz->title }}' />
    </x-nav.breadcrumb>
    <div class="grid grid-cols-3">
        <div class="rounded-lg shadow-md p-5 col-span-3 md:col-span-2">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $quiz->title }}</h1>
    
                @if($quiz->description)
                <p class="text-gray-600 dark:text-gray-300 mb-8 text-lg">{{ $quiz->description }}</p>
                @endif
    
                <div class="bg-blue-50 dark:bg-gray-500/20 rounded-lg p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-center">
                        <div class="bg-white dark:bg-gray-500/20 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600">{{ $totalQuestions }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Questions</div>
                        </div>
                        <div class="bg-white dark:bg-gray-500/20 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600">{{ $quiz->passing_grade }}%</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Passing Grade</div>
                        </div>
                    </div>
                </div>
    
                <div class="text-left bg-gray-50 dark:bg-gray-500/20 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-3 dark:text-white">Instructions:</h3>
                    <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-2">
                        <li>Read each question carefully before selecting your answer</li>
                        <li>You can navigate between questions using the navigation buttons</li>
                        <li>Make sure to answer all questions before submitting</li>
                        <li>You need {{ $quiz->passing_grade }}% or higher to pass</li>
                        <li>Once submitted, you cannot change your answers</li>
                    </ul>
                </div>
    
                <a href="{{ route('quiz.take-quiz' ,['quiz' => $quiz->id]) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-300">
                    Start Quiz
                </a>
            </div>
        </div>
        <div class="rounded-lg shadow-md-p-5 col-span-3 md:col-span-1 mt-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 md:mb-7 text-center">Attempt History</h2>
            <div class="bg-blue-50 dark:bg-gray-500/20 w-full p-3">
                <table class="table-auto w-full text-center">
                    <thead>
                        <tr>
                        <th>Date</th>
                        <th>Score</th>
                        <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$userAttempts)
                            <tr>
                                <td rowspan="2" class="p-4 text-center">
                                    <span>History empty</span>
                                </td>
                            </tr>
                        @endif
                        @foreach ($userAttempts as $attempt)
                            <tr>
                                <td>{{$attempt->created_at}}</td>
                                <td>{{$attempt->score}}</td>
                                <td class="{{ $attempt->score >= ($quiz->passing_grade ?? 0) ? 'text-green-500' : 'text-red-500' }}">{{$attempt->note}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>