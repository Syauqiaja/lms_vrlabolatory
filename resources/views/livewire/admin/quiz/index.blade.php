<?php

use Livewire\Volt\Component;
use App\Models\Activity;
use App\Models\Quiz;

new class extends Component {
    
    public $search;

    public function getQuizzes(){
        $quizzes = Quiz::query();
        if($this->search){
            $quizzes->where('title', 'like', '%'.$this->search.'%');
        }

        return $quizzes->get();
    }

    public function with()
    {
        return [
            'quizzes' => $this->getQuizzes(),
        ];
    }
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Quiz' />
    </x-nav.breadcrumb>

    <div class="flex items-center mb-8">
        <div>
            <span class="font-semibold text-xl block">Quiz</span>
            <span class="text-sm block text-gray-400">Quizzez that you might try</span>
        </div>
        <flux:spacer />
        <flux:button href="{{ route('admin.quiz.create') }}" icon='plus' variant="primary">
            Add Quiz
        </flux:button>
    </div>

    <!-- Search and Filter Bar -->
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center mb-8">
        <!-- Search Input -->
        <div class="flex-1 w-full sm:max-w-md">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari quiz..." icon="magnifying-glass" />
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 items-start sm:items-center mb-8">
    @for ($i = 0; $i < count($quizzes); $i++)
    <div class="col-span-2 md:col-span-1 p-4 border border-gray-300/30 rounded-md flex gap-3 items-center hover:bg-blue-50/10 relative">
        <a href="{{ route('admin.quiz.detail', ['quiz' => $quizzes[$i]->id]) }}" class="flex-1 flex gap-3 items-center">
            <div class="flex-1">
                <div class="text-start font-semibold">
                    {{ $quizzes[$i]->title }}
                </div>
                <div class="mt-4 flex gap-3">
                    <div class="rounded-full bg-blue-100 dark:bg-gray-600 px-4">
                        Total Questions : {{ count($quizzes[$i]->quizQuestions) }}
                    </div>
                    @if ($quizzes[$i]->passing_grade)
                    <div class="rounded-full bg-blue-100 dark:bg-green-900 px-4">
                        Passing Grade : {{ $quizzes[$i]->passing_grade }}
                    </div>
                    @endif
                </div>
            </div>
        </a>
        
        <!-- Three-dot menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-200">
                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                </svg>
            </button>
            
            <!-- Dropdown menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                
                <a href="{{ route('admin.quiz.create', ['quiz_id' => $quizzes[$i]->id]) }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Quiz
                </a>
                
                <form action="{{ route('admin.quiz.destroy', ['quiz' => $quizzes[$i]->id]) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this quiz?')" class="block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Quiz
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endfor
</div>
</div>
