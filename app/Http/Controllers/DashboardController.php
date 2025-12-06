<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
public function index()
{
$user = auth()->user();

// carica progetti con tasks e publications
$projects = $user->projects()->with(['tasks','publications'])->get();

// collezioni appiattite
$tasks = $projects->pluck('tasks')->flatten();
$publications = $projects->pluck('publications')->flatten();

return view('dashboard', compact('user','projects','tasks','publications'));
}
}
