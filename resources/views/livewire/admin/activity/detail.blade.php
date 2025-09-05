<?php

use Livewire\Volt\Component;
use App\Models\Activity;
use App\Models\Material;

new class extends Component {
    public Activity $activity;
    public $search;

    public function mount(Activity $activity){
        $this->activity = $activity;
        $this->materials = $activity->materials;
    }

    public function getMaterials(){
        $materials = Material::where('activity_id', $this->activity->id);
        if($this->search){
            $materials->where('title', 'like', '%'.$this->search.'%');
        }

        return $materials->get();
    }

    public function with()
    {
        return [
            'materials' => $this->getMaterials(),
        ];
    }
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Activities' href="{{ route('admin.activity') }}" />
        <x-nav.breadcrumb-item title='{{ $activity->title }}' />
    </x-nav.breadcrumb>

    <div class="flex flex-col sm:flex-row mb-8 w-full">
        <div class="sm:w-1/2 w-full">
            <img src="{{ Storage::url($activity->image) }}" alt="{{ $activity->title }}"
                class="object-cover w-full h-full rounded-md">
        </div>
        <div class="flex-1 p-4">
            <div class="font-bold text-lg mb-2">
                {{ $activity->title }}
            </div>
            <div>
                {{ $activity->description }}
            </div>
        </div>
    </div>

    <div class="flex items-center mb-8">
        <div>
            <span class="font-semibold text-xl block">Daftar Materi</span>
            <span class="text-sm block text-gray-400">{{$activity->title}}</span>
        </div>
        <flux:spacer />
        @role('admin')
        <div class="flex gap-3">
            <flux:button variant="outline" href="{{ route('admin.material.create', ['activity' => $activity->id]) }}"
                iconTrailing='plus'>
                Tambah Materi
            </flux:button>
        </div>
        @endrole
    </div>

    <!-- Search and Filter Bar -->
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center mb-8">
        <!-- Search Input -->
        <div class="flex-1 w-full sm:max-w-md">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari materi..." icon="magnifying-glass" />
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center mb-8">
        @for ($i = 0; $i < count($materials); $i++) <a
            href="{{ route('admin.material.detail', ['activity' => $activity->id, 'material' => $materials[$i]->id]) }}"
            class="flex-1 w-full sm:max-w-xl p-4 border border-gray-300/30 rounded-md flex gap-3 items-center hover:bg-blue-50/10">
            <div class="rounded-full bg-gray-300/50 flex justify-center items-center w-8 h-8 flex-none">
                <span class="font-bold">{{$i + 1}}</span>
            </div>
            <div class="flex-1">
                <div class="text-start font-medium">
                    {{ $materials[$i]->title }}
                </div>
            </div>
            </a>
            @endfor
    </div>

</div>