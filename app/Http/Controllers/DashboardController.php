<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Milestone; // <-- IMPORTANTE: Aggiungi l'import del modello Milestone

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. CARICAMENTO PROGETTI E PUBBLICAZIONI
        // Manteniamo questa parte per le altre sezioni della dashboard.
        $projects = $user->projects()->with('publications')->get();
        $publications = $projects->pluck('publications')->flatten();

        // 2. CALCOLO KPI (I contatori per le card)
        // Totale assegnate a me
        $assignedTasksCount = Task::where('assignee_id', $user->id)->count();

        // Totale "In Corso" (Assegnate + Non completate)
        $scheduledTasksCount = Task::where('assignee_id', $user->id)
            ->where('status', '!=', 'done')
            ->count();

        // 3. LISTA TASK PER LA TABELLA
        // Prendiamo solo le tue, ordinate per scadenza, max 10
        $myTasks = Task::where('assignee_id', $user->id)
            ->with('project') // Eager loading per evitare query N+1
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get();

        // 4. LISTA MILESTONE (NUOVA SEZIONE)
        // Recuperiamo gli ID dei progetti a cui l'utente è associato
        $projectIds = $projects->pluck('id');

        // Prendiamo le prossime 5 milestone di questi progetti
        $milestones = Milestone::whereIn('project_id', $projectIds)
            ->with('project') // Eager loading per stampare il nome del progetto
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'projects',
            'publications',
            'myTasks',            // Variabile per la tabella task
            'assignedTasksCount', // Variabile per la card 1
            'scheduledTasksCount',// Variabile per la card 2
            'milestones'          // <-- NUOVA variabile per la card delle Milestone
        ));
    }
}
