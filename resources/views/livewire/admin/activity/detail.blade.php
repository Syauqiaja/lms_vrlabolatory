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
        <x-nav.breadcrumb-item title='{{ $activity->title }}'/>
    </x-nav.breadcrumb>
</div>
