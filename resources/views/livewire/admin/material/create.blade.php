<?php

use Livewire\Volt\Component;
use App\Models\Activity;
use App\Models\Material;

new class extends Component {
    public Activity $activity;
    public $title;
    public $content;
    public function mount(Activity $activity){
        $this->activity = $activity;
    }
    public function save(){

    }
}; ?>

<div>
    <x-nav.breadcrumb>
        <x-nav.breadcrumb-item title='Activities' href="{{ route('admin.activity') }}" />
        <x-nav.breadcrumb-item title='{{ $activity->title }}'
            href="{{ route('admin.activity.detail', ['activity' => $activity->id]) }}" />
        <x-nav.breadcrumb-item title='Materi'
            href="{{ route('admin.material.index', ['activity' => $activity->id]) }}" />
        <x-nav.breadcrumb-item title='Create' />
    </x-nav.breadcrumb>


    <div class="flex items-center mb-8">
        <div>
            <span class="font-semibold text-xl block">Tambahkan Materi Baru</span>
            <span class="text-sm block text-gray-400">Buat materi baru untuk aktivitas : {{$activity->title}}</span>
        </div>
        <flux:spacer />
        <div class="flex gap-3">
            <flux:button variant="ghost" wire:click="resetForm" iconTrailing='arrow-uturn-down'>
                Reset
            </flux:button>
        </div>
    </div>
    <form wire:submit='save'>
        <div>
            <div class="mb-2 text-md font-bold">
                <label for="inputTitle">Judul Materi</label>
            </div>
            <div class="flex-1 w-full sm:max-w-lg">
                <flux:input wire:model="title" placeholder="Masukkan judul materi" id="inputTitle"/>
            </div>
        </div>
        <div class="w-full mt-5">
            <div class="mb-2 text-md font-bold">
                <label for="inputTitle">Konten</label>
            </div>
            <div class="w-full dark:text-white">
                <div id="editor">
                    <p>Masukkan konten materi anda di sini</p>
                </div>
            </div>
        </div>

    </form>

</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        const quill = new Quill('#editor', {
            theme: 'snow'
        });
    </script>
@endpush