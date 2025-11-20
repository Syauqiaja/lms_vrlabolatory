<?php

use App\Http\Controllers\Quiz\QuizController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');


    Volt::route('activity', 'activity.index')->name('activity');
    Volt::route('activity/{activity}', 'activity.detail')->name('activity.detail');


    Volt::route('activity/{activity}/materials', 'material.index')->name('material.index');
    Volt::route('activity/{activity}/materials/{material}', 'material.detail')->name('material.detail');

    Volt::route('quiz', 'quiz.index')->name('quiz');
    Volt::route('quiz/{quiz}', 'quiz.detail')->name('quiz.detail');
    Volt::route('quiz/{quiz}/take', 'quiz.take-quiz')->name('quiz.take-quiz');

    Volt::route('lab', 'lab.index')->name('lab');
    Volt::route('lab/{workStepGroup}', 'lab.detail')->name('lab.detail');
    Volt::route('user', 'users.detail')->name('user.detail');
    Volt::route('dashboard', 'dashboard')
        ->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function(){
    Volt::route('admin/activity', 'activity.index')->name('admin.activity');
    Volt::route('admin/activity/create', 'activity.create')->name('admin.activity.create');
    Volt::route('admin/activity/{activity}', 'activity.detail')->name('admin.activity.detail');
    Volt::route('admin/activity/{activity}/edit', 'activity.edit')->name('admin.activity.edit');

    Volt::route('admin/lab', 'lab.index')->name('admin.lab');
    Volt::route('admin/lab/{workStepGroup}', 'admin.lab.detail')->name('admin.lab.detail');

    Volt::route('admin/activity/{activity}/materials', 'material.index')->name('admin.material.index');
    Volt::route('admin/activity/{activity}/materials/create', 'material.create')->name('admin.material.create');
    Volt::route('admin/activity/{activity}/materials/{material}', 'material.detail')->name('admin.material.detail');
    Volt::route('admin/activity/{activity}/materials/{material}/edit', 'material.edit')->name('admin.material.edit');

    Volt::route('admin/quiz', 'quiz.index')->name('admin.quiz');
    Volt::route('admin/quiz/create', 'quiz.create')->name('admin.quiz.create');
    Volt::route('admin/quiz/{quiz}', 'admin.quiz.detail')->name('admin.quiz.detail');
    Route::delete('admin/quiz/{quiz}/destroy', [QuizController::class, 'destroy'])->name('admin.quiz.destroy');
    Volt::route('admin/quiz/{quiz}/edit', 'quiz.edit')->name('admin.quiz.edit');

    Volt::route('admin/users', 'admin.users')->name('admin.users');
    Volt::route('admin/users/{user}', 'admin.users.detail')->name('admin.user.detail');
    Volt::route('admin/users/{user}/lab/{workStepGroup}', 'admin.users.lab')->name('admin.users.lab');
});

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});

require __DIR__.'/auth.php';
