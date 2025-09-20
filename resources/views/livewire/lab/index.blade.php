<?php

use Livewire\Volt\Component;
use App\Models\WorkStepGroup;

new class extends Component {
    public $search;
    public $workStepGroups;
    public $user;

    public function mount(){
        $this->workStepGroups = WorkStepGroup::all();
        $this->user = Auth::user();
    }
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Lab' />
    </x-nav.breadcrumb>

    <div class="flex items-center mb-8">
        <div>
            <span class="font-semibold text-xl block">Daftar Praktikum</span>
            <span class="text-sm block text-gray-400">Seluruh praktikum yang bisa anda ambil</span>
        </div>
        <flux:spacer />
    </div>

    <!-- Search and Filter Bar -->
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center mb-8">
        <!-- Search Input -->
        <div class="flex-1 w-full sm:max-w-md">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari materi..." icon="magnifying-glass" />
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 items-start sm:items-center mb-8">
        @for ($i = 0; $i < count($workStepGroups); $i++) <a
            href="{{ route('lab.detail', ['workStepGroup' => $workStepGroups[$i]->id]) }}"
            class="col-span-2 md:col-span-1 p-4 border border-gray-300/30 rounded-md flex gap-3 items-center hover:bg-blue-50/10">
            @php
            $steps = $workStepGroups[$i]->workSteps()->count();
            $completion = $workStepGroups[$i]->workSteps()->whereHas('userWorksCompletions', function($q)use ($user){
            $q->where('user_id', $user->id);
            })->count();
            $progress = ceil(($completion / $steps) * 100);
            @endphp

            <div class="relative flex justify-center items-center w-16 h-16">
                <svg class="w-16 h-16 transform -rotate-90">
                    <!-- Background circle -->
                    <circle class="text-gray-300" stroke-width="4" stroke="currentColor" fill="transparent" r="28"
                        cx="32" cy="32" />
                    <!-- Progress circle -->
                    <circle class="text-blue-500" stroke-width="4" stroke-dasharray="175.9"
                        stroke-dashoffset="{{ 175.9 - (175.9 * $progress / 100) }}" stroke="currentColor"
                        fill="transparent" r="28" cx="32" cy="32" />
                </svg>
                <!-- Percentage text inside -->
                <span class="absolute font-bold text-sm text-gray-700 dark:text-gray-300">
                    {{ $progress }}%
                </span>
            </div>
            <div class="flex-1">
                <div class="text-start font-medium">
                    {{ $workStepGroups[$i]->title }}
                </div>
            </div>
            </a>
            @endfor
    </div>
</div>