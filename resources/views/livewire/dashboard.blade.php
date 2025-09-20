<?php

use Livewire\Volt\Component;
use App\Models\Activity;
use App\Models\Quiz;
use App\Models\User;

new class extends Component {
    public $totalActivities;
    public $totalQuiz;
    public $totalUsers;

    public function mount(){
        $this->totalActivities = Activity::count();
        $this->totalQuiz = Quiz::count();
        $this->totalUsers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->count();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <h5 class="text-3xl font-medium">Selamat Datang</h5>
    <h5 class="text-2xl font-medium">{{Auth::user()->name}}</h5>
    <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
    </div>
    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div
            class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center align-center">
            <h5 class="text-xl text-center">Total Aktivitas</h5>
            <h5 class="text-3xl mt-3 font-semibold text-center">{{$totalActivities}}</h5>
        </div>
        <div
            class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center align-center">
            <h5 class="text-xl text-center">Total Quiz</h5>
            <h5 class="text-3xl mt-3 font-semibold text-center">{{$totalQuiz}}</h5>
        </div>
        <div
            class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col justify-center align-center">
            <h5 class="text-xl text-center">Total User</h5>
            <h5 class="text-3xl mt-3 font-semibold text-center">{{$totalUsers}}</h5>
        </div>
    </div>
</div>