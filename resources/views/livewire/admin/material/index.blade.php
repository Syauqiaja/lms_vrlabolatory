<?php

use Livewire\Volt\Component;
use App\Models\Activity;
use App\Models\Material;

new class extends Component {
    public Activity $activity;
    public function mount(Activity $activity){
        $this->activity = $activity;
    }
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Activities' href="{{ route('admin.activity') }}" />
        <x-nav.breadcrumb-item title='{{ $activity->title }}'
            href="{{ route('admin.activity.detail', ['activity' => $activity->id]) }}" />
        <x-nav.breadcrumb-item title='Materi' />
    </x-nav.breadcrumb>


    <div class="flex items-center mb-8">
        <div>
            <span class="font-semibold text-xl block">Daftar Materi</span>
            <span class="text-sm block text-gray-400">{{$activity->title}}</span>
        </div>
        <flux:spacer />
        <div class="flex gap-3">
            <flux:button variant="outline" href="{{ route('admin.material.create', ['activity' => $activity->id]) }}" iconTrailing='plus'>
                Tambah Materi
            </flux:button>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center mb-8">
        <!-- Search Input -->
        <div class="flex-1 w-full sm:max-w-md">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari materi..."
                icon="magnifying-glass" />
        </div>
    </div>

</div>