<?php

use Livewire\Volt\Component;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

new class extends Component {
    public $quiz_index;
    public $quiz;
    public $questions = [];
    public $option_texts = [];
    public $options = [];
    public $isEditing = false;
    public $title;
    public $passingGrade;

    public function mount(Request $request) {
        $this->quiz_index = $request->quiz_id;
        $this->loadQuiz();
        
        // Initialize arrays if creating new quiz
        if (!$this->isEditing) {
            $this->questions = array_fill(0, 1, '');
            $this->option_texts = array_fill(0, 1, ['A' => '', 'B' => '', 'C' => '', 'D' => '']);
            $this->options = array_fill(0, 1, null);
        }
    }

    public function loadQuiz() {
        // Try to find existing quiz
        $this->quiz = Quiz::find($this->quiz_index);
        
        if ($this->quiz) {
            $this->isEditing = true;
            $this->title = $this->quiz->title;
            $this->passingGrade = $this->quiz->passing_grade;
            $quizQuestions = QuizQuestion::where('quiz_id', $this->quiz->id)
                                       ->orderBy('order')
                                       ->get();
            
            // Load existing data
            foreach ($quizQuestions as $index => $question) {
                $this->questions[$index] = $question->question;
                $this->option_texts[$index] = [
                    'A' => $question->answer_a,
                    'B' => $question->answer_b,
                    'C' => $question->answer_c,
                    'D' => $question->answer_d,
                ];
                $this->options[$index] = $question->correct_answer;
            }
        }
    }

    public function changeOption($index, $option) {
        $this->options[$index] = $option;
    }

    public function save() {
        // Validate input
        $this->validate([
            'questions.*' => 'required|string|max:500',
            'option_texts.*.*' => 'required|string|max:200',
            'options.*' => 'required|in:A,B,C,D',
        ]);

        try {
            \DB::transaction(function () {
                // Create or update quiz
                if ($this->isEditing) {
                    $quiz = $this->quiz;
                    $quiz->passing_grade = $this->passingGrade;
                    $quiz->title = $this->title;
                    $quiz->save();
                } else {
                    $quiz = Quiz::create([
                        'title' => $this->title,
                        'order' => $this->quiz_index,
                        'passing_grade' => $this->passingGrade,
                    ]);
                }

                // Delete existing questions if editing
                if ($this->isEditing) {
                    QuizQuestion::where('quiz_id', $quiz->id)->delete();
                }

                // Create new questions
                for ($i = 0; $i < count($this->questions); $i++) {
                    if (!empty($this->questions[$i])) {
                        QuizQuestion::create([
                            'quiz_id' => $quiz->id,
                            'question' => $this->questions[$i],
                            'answer_a' => $this->option_texts[$i]['A'] ?? '',
                            'answer_b' => $this->option_texts[$i]['B'] ?? '',
                            'answer_c' => $this->option_texts[$i]['C'] ?? '',
                            'answer_d' => $this->option_texts[$i]['D'] ?? '',
                            'order' => $i + 1,
                            'correct_answer' => $this->options[$i],
                        ]);
                    }
                }
            });

            session()->flash('success', $this->isEditing ? 'Quiz updated successfully!' : 'Quiz created successfully!');
            
            // Redirect to quiz list or stay on current page
            return redirect()->route('admin.quiz');
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while saving the quiz '.$e->getMessage());
            \Log::error('Quiz save error: ' . $e->getMessage());
        }
    }

    public function delete() {
        if ($this->quiz) {
            try {
                \DB::transaction(function () {
                    // Delete questions first (foreign key constraint)
                    QuizQuestion::where('quiz_id', $this->quiz->id)->delete();
                    // Delete quiz
                    $this->quiz->delete();
                });

                session()->flash('success', 'Quiz deleted successfully!');
                return redirect()->route('admin.quiz');
                
            } catch (\Exception $e) {
                session()->flash('error', 'An error occurred while deleting the quiz.');
                \Log::error('Quiz delete error: ' . $e->getMessage());
            }
        }
    }

    public function addQuestion(){
        $this->questions[] = '';
        $this->option_texts[] = ['A' => '', 'B' => '', 'C' => '', 'D' => ''];
        $this->options[] = null;
    }
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Quiz' href="{{ route('admin.quiz') }}" />
        <x-nav.breadcrumb-item title="{{ $isEditing ? 'Update' : 'Create' }}" />
    </x-nav.breadcrumb>

    <div class="flex items-center mb-8">
        <div>
            <span class="font-semibold text-xl block">Buat Quiz</span>
            <span class="text-sm block text-gray-400">Tambahkan quiz baru</span>
        </div>
        <flux:spacer />
        <div class="flex gap-3">
            <flux:button variant="ghost" wire:click="resetForm" iconTrailing='arrow-uturn-down'>
                Reset
            </flux:button>
            <flux:button variant="primary" iconTrailing="check" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $isEditing ? 'Update Quiz' : 'Save Quiz' }}</span>
                <span wire:loading>Saving...</span>
            </flux:button>
        </div>
    </div>

    <div>
        @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <div class="mb-4 md:max-w-xl">
            <flux:input wire:model="title" label="Quiz Title"
                    placeholder="Insert your quiz title" type="text" required class="mb-4"/>
            <flux:input wire:model="passingGrade" label="Passing Grade"
                    placeholder="Insert the passing grade (optional)" type="number" required class="max-w-sm" />
        </div>

        <div class="flex gap-4 flex-col">
            @for ($i = 0; $i < count($questions); $i++) <div class="flex gap-3 flex-col border dark:border-gray-300/50 p-4 rounded">
                <flux:input wire:model="questions.{{ $i }}" :label="'Question '.($i+1)"
                    placeholder="Masukkan pertanyaan" type="text" required />

                @error("questions.{$i}")
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror

                @foreach (['A', 'B', 'C', 'D'] as $option)
                <flux:input.group class="w-lg">
                    <flux:button variant="{{ $options[$i] == $option ? 'primary' : 'outline' }}"
                        color="{{ $options[$i] == $option ? 'green' : 'black' }}"
                        wire:click="changeOption({{ $i }}, '{{ $option }}')">
                        {{ $option }}
                    </flux:button>
                    <flux:input wire:model="option_texts.{{ $i }}.{{ $option }}" placeholder="Jawaban {{ $option }}"
                        required />
                </flux:input.group>

                @error("option_texts.{$i}.{$option}")
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                @endforeach

                @error("options.{$i}")
                <span class="text-red-500 text-sm">Please select the correct answer for question {{ $i + 1 }}</span>
                @enderror
        </div>
        @endfor

        <div class="flex gap-2">
            <flux:button variant="outline" iconTrailing="plus" class="flex-1" wire:click="addQuestion" wire:loading.attr="disabled">
                <span>Add Question</span>
            </flux:button>

            @if($isEditing)
            <flux:button variant="danger" wire:click="delete"
                wire:confirm="Are you sure you want to delete this quiz? This action cannot be undone."
                wire:loading.attr="disabled">
                Delete Quiz
            </flux:button>
            @endif
        </div>
    </div>
</div>
</div>