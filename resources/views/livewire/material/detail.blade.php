<?php

use Livewire\Volt\Component;
use App\Models\Activity;
use App\Models\Material;

new class extends Component {
    public Activity $activity;
    public Material $material;

    public function mount(Activity $activity, Material $material){
        $this->activity = $activity;
        $this->material = $material;
    }

    public function previous(){
        $this->redirect(route(
            'material.detail', 
            ['activity' => $this->activity->id, 'material' => $this->material->previous()->id],
            ),
        );
    }
    public function next(){
        $this->redirect(route(
            'material.detail', 
            ['activity' => $this->activity->id, 'material' => $this->material->next()->id],
            ),
        );
    }
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Activities' href="{{ route('activity') }}" />
        <x-nav.breadcrumb-item title='{{ $activity->title }}'
            href="{{ route('activity.detail', ['activity' => $activity->id]) }}" />
        <x-nav.breadcrumb-item title='{{ $material->title }}' />
    </x-nav.breadcrumb>

    <div class="flex items-center">
        <div>
            <span class="font-semibold text-xl block">{{$material->title}}</span>
            <span class="text-sm block text-gray-400">{{$activity->title}}</span>
        </div>
        <flux:spacer />
        <div class="flex-row gap-4 hidden md:flex">
            @if ($material->previous())
            <flux:button class="w-full" variant="outline" wire:click='previous' iconLeading='arrow-left'>
                Kembali
            </flux:button>
            @endif
            @if ($material->next())
            <flux:button class="w-full" variant="outline" wire:click='next' iconTrailing='arrow-right'>
                Berikutnya
            </flux:button>
            @endif
        </div>
    </div>

    <div class="my-4 md:my-8">
        {!! $material->content !!}
    </div>


    @role('admin')
    <div class="flex-row gap-4 flex md:hidden w-full mt-8">
        @if ($material->previous())
        <flux:button class="w-full" variant="outline" wire:click='previous' iconLeading='arrow-left'>
            Kembali
        </flux:button>
        @endif
        @if ($material->next())
        <flux:button class="w-full" variant="outline" wire:click='next' iconTrailing='arrow-right'>
            Berikutnya
        </flux:button>
        @endif
    </div>
    @endrole
</div>