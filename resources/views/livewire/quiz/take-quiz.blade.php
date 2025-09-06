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

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz;
        $this->questions = $quiz->quizQuestions()->orderBy('order')->get();
        $this->totalQuestions = $this->questions->count();
        
        // Check if user has already completed this quiz
        $this->existingResult = UserQuizResult::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->first();
            
        if ($this->existingResult) {
            $this->hasCompleted = true;
            $this->score = $this->existingResult->score;
        }
    }
    public function startQuiz()
    {
        $this->hasStarted = true;
        $this->timeRemaining = $this->quizDuration * 60; // Convert to seconds
    }

    public function selectAnswer($questionId, $answer)
    {
        $this->answers[$questionId] = $answer;
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < $this->totalQuestions - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion($index)
    {
        $this->currentQuestionIndex = $index;
    }

    public function submitQuiz()
    {
        $correctAnswers = 0;
        
        foreach ($this->questions as $question) {
            $userAnswer = $this->answers[$question->id] ?? null;
            $isCorrect = false;
            
            // Determine correct answer (you may need to add a 'correct_answer' field to your QuizQuestion model)
            // For now, assuming answer_a is always correct - you should modify this logic
            if ($userAnswer && $userAnswer === $question->correct_answer) {
                $isCorrect = true;
                $correctAnswers++;
            }
            
            // Save user's answer
            UserQuestionAnswer::updateOrCreate([
                'user_id' => Auth::id(),
                'quiz_question_id' => $question->id,
            ], [
                'answer' => $userAnswer,
                'is_correct' => $isCorrect,
            ]);
        }
        
        $this->score = round(($correctAnswers / $this->totalQuestions) * 100);
        
        // Save quiz result
        UserQuizResult::updateOrCreate([
            'user_id' => Auth::id(),
            'quiz_id' => $this->quiz->id,
        ], [
            'score' => $this->score,
            'note' => $this->score >= ($this->quiz->passing_grade ?? 0) ? 'Passed' : 'Failed',
        ]);
        
        \Masmerise\Toaster\Toaster::success('Congratulation, you finished the quiz!');
        $this->redirect(route('admin.quiz.detail', ['quiz' => $this->quiz->id]));
    }

    public function retakeQuiz()
    {
        // Delete previous answers and results
        UserQuestionAnswer::where('user_id', Auth::id())
            ->whereIn('quiz_question_id', $this->questions->pluck('id'))
            ->delete();
            
        UserQuizResult::where('user_id', Auth::id())
            ->where('quiz_id', $this->quiz->id)
            ->delete();
            
        // Reset component state
        $this->answers = [];
        $this->currentQuestionIndex = 0;
        $this->showResults = false;
        $this->score = 0;
        $this->hasStarted = false;
        $this->hasCompleted = false;
        $this->existingResult = null;
    }
}; ?>

<div>
    <div class="bg-white dark:bg-transparent dark:shadow-none rounded-lg shadow-md">
        <!-- Quiz Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $quiz->title }}</h1>
                <div class="text-sm text-gray-500 dark:text-gray-300">
                    Question {{ $currentQuestionIndex + 1 }} of {{ $totalQuestions }}
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        style="width: {{ (($currentQuestionIndex + 1) / $totalQuestions) * 100 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Question Content -->
        <div class="p-6">
            @if(isset($questions[$currentQuestionIndex]))
            @php $question = $questions[$currentQuestionIndex] @endphp

            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
                    {{ $question->question }}
                </h2>

                <div class="space-y-3">
                    @foreach(['a', 'b', 'c', 'd'] as $option)
                    @if($question->{'answer_' . $option})
                    <label wire:click="selectAnswer({{ $question->id }}, '{{ $option }}')"
                        class="flex items-start pl-4 pr-4 py-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-500/20 transition duration-200
                                            {{ ($answers[$question->id] ?? '') === $option ? 'border-blue-500 bg-blue-50 dark:bg-transparent' : 'border-gray-200' }}">
                        <span class="text-gray-700 my-auto dark:text-white">
                            <span class="font-medium">{{ strtoupper($option) }}.</span>
                            {{ $question->{'answer_' . $option} }}
                        </span>
                        <flux:spacer />
                        <div class="my-auto {{ ($answers[$question->id] ?? '') === $option ? '' : 'hidden' }}">
                            <div
                                class="w-6 h-6 rounded-full {{ ($answers[$question->id] ?? '') === $option ? 'bg-green-600' : '' }} flex justify-center items-center">
                                <flux:icon icon="check" class="w-4 h-4" />
                            </div>
                        </div>
                    </label>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Navigation -->
        <div class="border-t border-gray-200 px-6 py-4">
            <div class="flex justify-between items-center">
                <flux:button wire:click="previousQuestion" variant="primary" iconLeading="arrow-left" class="{{ $currentQuestionIndex === 0 ? 'opacity-50 cursor-not-allowed disabled' : '' }}">
                    Previous
                </flux:button>

                <!-- Question Numbers -->
                <div class="flex space-x-2">
                    @for($i = 0; $i < $totalQuestions; $i++) <button wire:click="goToQuestion({{ $i }})" class="w-8 h-8 rounded-full text-sm font-medium transition duration-200
                                        @if($i === $currentQuestionIndex)
                                            bg-blue-600 text-white
                                        @elseif(isset($answers[$questions[$i]->id]))
                                            bg-green-100 text-green-800 hover:bg-green-200
                                        @else
                                            bg-gray-100 text-gray-600 hover:bg-gray-200
                                        @endif">
                        {{ $i + 1 }}
                        </button>
                        @endfor
                </div>

                @if($currentQuestionIndex === $totalQuestions - 1)

                <flux:button wire:click="submitQuiz" variant="primary">
                    Submit Quiz
                </flux:button>
                @else
                <flux:button wire:click="nextQuestion" variant="primary" iconTrailing="arrow-right">
                    Next
                </flux:button>
                @endif
            </div>
        </div>
    </div>
</div>