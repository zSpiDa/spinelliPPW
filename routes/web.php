<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ProjectController, PageController, PublicationController, TaskController};
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;


Route::get('/', [PageController::class, 'home']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'role:admin,pi,manager'])->group(function () {
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/projects/{project}/members', [ProjectController::class, 'addMember'])->name('projects.addMember');
    Route::delete('/projects/{project}/members/{user}', [ProjectController::class, 'removeMember'])->name('projects.removeMember');
    Route::post('/projects/{project}/members/sync', [ProjectController::class, 'syncMembers'])->name('projects.sync');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('projects', ProjectController::class)->except(['store', 'create', 'destroy']);
    Route::resource('publications', PublicationController::class);
    Route::resource('tasks', TaskController::class);
    Route::get('/projects/json', [ProjectController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users/edit', [UserController::class, 'edit'])->name('users.edit');
});

require __DIR__.'/auth.php';
