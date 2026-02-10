<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ProjectController,PageController,PublicationController,TaskController};
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [PageController::class, 'home']);

/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('projects', ProjectController::class)->except(['store', 'create', 'destroy']);
    Route::resource('publications', PublicationController::class);
    Route::resource('tasks', TaskController::class);
    Route::get('/projects/json', [ProjectController::class, 'index']);
});

Route::middleware(['auth', 'role:admin,pi,manager'])->group(function () {
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/users', [UserController::class, 'index'])
    ->middleware(['auth', 'role:admin,pi'])
    ->name('users.index');

Route::get('/users/edit', [UserController::class, 'edit'])
    ->middleware(['auth'])
    ->name('users.edit');


Route::post('/projects/{project}/members', [ProjectController::class, 'addMember'])
        ->middleware(['auth', 'role:admin,pi'])
        ->name('projects.addMember');

Route::delete('/projects/{project}/members/{user}', [ProjectController::class, 'removeMember'])
        ->middleware(['auth', 'role:admin,pi'])
        ->name('projects.removeMember');

Route::post('/projects/{project}/members/sync', [ProjectController::class, 'syncMembers'])
        ->middleware(['auth', 'role:admin,pi'])
        ->name('projects.sync');


require __DIR__.'/auth.php';
