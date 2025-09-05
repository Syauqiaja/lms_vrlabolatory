<?php

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
});

require __DIR__.'/auth.php';
