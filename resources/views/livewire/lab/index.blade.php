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

    <x-work-group-item :workStepGroups='$workStepGroups' :user='$user'/>
</div>