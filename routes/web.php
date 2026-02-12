<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ProjectController, PageController, PublicationController, TaskController};
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\Auth\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home']);

// --- ROTTE PROFILO UTENTE (Standard) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ======================================================================
//  ZONA PROTETTA (Admin, PI, Manager)
//  Qui ci sono le azioni di SCRITTURA (Create, Edit, Update, Delete)
// ======================================================================
Route::middleware(['auth', 'role:admin,pi,manager'])->group(function () {

    // --- PROGETTI (Gestione Completa) ---
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Gestione Membri Progetto
    Route::post('/projects/{project}/members', [ProjectController::class, 'addMember'])->name('projects.addMember');
    Route::delete('/projects/{project}/members/{user}', [ProjectController::class, 'removeMember'])->name('projects.removeMember');
    Route::post('/projects/{project}/members/sync', [ProjectController::class, 'syncMembers'])->name('projects.sync');

    // --- TASKS (Solo Modifica ed Eliminazione) ---
    // Nota: La creazione (store) è lasciata sotto per tutti, o puoi spostarla qui se vuoi.
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // --- MILESTONES (Gestione Completa) ---
    Route::post('/projects/{project}/milestones', [MilestoneController::class, 'store'])->name('projects.milestones.store');
    Route::get('/milestones/{milestone}/edit', [MilestoneController::class, 'edit'])->name('milestones.edit');
    Route::put('/milestones/{milestone}', [MilestoneController::class, 'update'])->name('milestones.update');
    Route::delete('/milestones/{milestone}', [MilestoneController::class, 'destroy'])->name('milestones.destroy');

    // --- UTENTI (Gestione) ---
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
});

// ======================================================================
//  ZONA GENERICA (Autenticati)
//  Qui ci sono le azioni di LETTURA (Index, Show) accessibili a tutti
// ======================================================================
Route::middleware(['auth'])->group(function () {

    // Progetti: Tutti possono vedere, ma le azioni di modifica sono escluse qui (perché gestite sopra)
    Route::resource('projects', ProjectController::class)
        ->except(['create', 'store', 'edit', 'update', 'destroy']);

    // Tasks: Tutti possono vedere e creare, ma non modificare/cancellare (gestito sopra)
    Route::resource('tasks', TaskController::class)
        ->except(['edit', 'update', 'destroy']);

    // Pubblicazioni (Accesso completo per ora, restringi se necessario)
    Route::resource('publications', PublicationController::class);

    // Altre rotte di lettura
    Route::get('/projects/json', [ProjectController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users/edit', [UserController::class, 'edit'])->name('users.edit');
});

require __DIR__.'/auth.php';
