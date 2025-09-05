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
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Activities' href="{{ route('admin.activity') }}" />
        <x-nav.breadcrumb-item title='{{ $activity->title }}'
            href="{{ route('admin.activity.detail', ['activity' => $activity->id]) }}" />
        <x-nav.breadcrumb-item title='{{ $material->title }}' />
    </x-nav.breadcrumb>
</div>
