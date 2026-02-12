<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task; // Assicurati di importare il modello Task

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. CARICAMENTO PROGETTI E PUBBLICAZIONI
        // Manteniamo questa parte per le altre sezioni della dashboard.
        // Nota: Ho rimosso 'tasks' dal with() perché le carichiamo separatamente e meglio sotto.
        $projects = $user->projects()->with('publications')->get();
        $publications = $projects->pluck('publications')->flatten();

        // 2. CALCOLO KPI (I contatori per le card)
        // Usiamo una query diretta su Task per efficienza

        // Totale assegnate a me
        $assignedTasksCount = Task::where('assignee_id', $user->id)->count();

        // Totale "In Corso" (Assegnate + Non completate)
        $scheduledTasksCount = Task::where('assignee_id', $user->id)
            ->where('status', '!=', 'done')
            ->count();

        // 3. LISTA TASK PER LA TABELLA
        // Prendiamo solo le tue, ordinate per scadenza, max 10
        $myTasks = Task::where('assignee_id', $user->id)
            ->with('project') // Eager loading per evitare query N+1 quando fai $task->project->title
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'user',
            'projects',
            'publications',
            'myTasks',            // La nuova variabile per la tabella
            'assignedTasksCount', // Variabile per la card 1
            'scheduledTasksCount' // Variabile per la card 2
        ));
    }
}
