<?php

use Livewire\Volt\Component;
use App\Models\WorkStepGroup;

new class extends Component {
    public $search;
    public $workStepGroups;

    public function mount(){
        $this->workStepGroups = WorkStepGroup::all();
    }
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Lab'/>
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
            <div class="rounded-full bg-gray-300/50 flex justify-center items-center w-8 h-8 flex-none">
                <span class="font-bold">{{$workStepGroups[$i]->order}}</span>
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
