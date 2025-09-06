<?php

use App\Http\Controllers\Quiz\QuizController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'welcome')->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('quiz/{quiz}/take', 'quiz.take-quiz')->name('quiz.take-quiz');
});

Route::middleware(['auth', 'role:admin'])->group(function(){
    Volt::route('admin/activity', 'admin.activity.index')->name('admin.activity');
    Volt::route('admin/activity/create', 'admin.activity.create')->name('admin.activity.create');
    Volt::route('admin/activity/{activity}', 'admin.activity.detail')->name('admin.activity.detail');
    Volt::route('admin/activity/{activity}/edit', 'admin.activity.edit')->name('admin.activity.edit');

    Volt::route('admin/activity/{activity}/materials', 'admin.material.index')->name('admin.material.index');
    Volt::route('admin/activity/{activity}/materials/create', 'admin.material.create')->name('admin.material.create');
    Volt::route('admin/activity/{activity}/materials/{material}', 'admin.material.detail')->name('admin.material.detail');
    Volt::route('admin/activity/{activity}/materials/{material}/edit', 'admin.material.edit')->name('admin.material.edit');

    Volt::route('admin/quiz', 'admin.quiz.index')->name('admin.quiz');
    Volt::route('admin/quiz/create', 'admin.quiz.create')->name('admin.quiz.create');
    Volt::route('admin/quiz/{quiz}', 'admin.quiz.detail')->name('admin.quiz.detail');
    Route::delete('admin/quiz/{quiz}/destroy', [QuizController::class, 'destroy'])->name('admin.quiz.destroy');
    Volt::route('admin/quiz/{quiz}/edit', 'admin.quiz.edit')->name('admin.quiz.edit');
});

require __DIR__.'/auth.php';
