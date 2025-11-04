<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

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
Route::get('/', [PageController::class, 'home']);;

Route::get('/projects', [PageController::class, 'projects']);;

Route::get('/welcome', function(){
    return view('welcome');
});

Route::get('/about', [PageController::class, 'about'])->name('about.page');

Route::prefix('admin')->group(function () {
    Route::get('/projects', function () {
        return '<h1>Gestione progetti (Area admin)</h1><p>Elenco e gestione progetti di ricerca.</p>';
    });
    Route::get('/users', function () {
        return '<h1>Gestione utenti (Area admin)</h1><p>Elenco e gestione utenti del sistema.</p>';
    });
});

Route::get('/projects/{name}', [PageController::class, 'showProjects']);
Route::get('/projects/json', [ProjectController::class, 'index']);
