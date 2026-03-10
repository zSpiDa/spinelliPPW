<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Milestone;
use App\Models\Publication; // <-- Aggiunto per le pubblicazioni
use Carbon\Carbon;          // <-- Aggiunto per calcolare i 7 giorni

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. CARICAMENTO PROGETTI E PUBBLICAZIONI
        $projects = $user->projects()->with('publications')->get();
        $publications = $projects->pluck('publications')->flatten();

        // 2. CALCOLO KPI (I contatori per le card)
        $assignedTasksCount = Task::where('assignee_id', $user->id)->count();

        $scheduledTasksCount = Task::where('assignee_id', $user->id)
            ->where('status', '!=', 'done')
            ->count();

        // 3. LISTA TASK PER LA TABELLA
        $myTasks = Task::where('assignee_id', $user->id)
            ->with('project')
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get();

        // 4. LISTA MILESTONE
        $projectIds = $projects->pluck('id');

        $milestones = Milestone::whereIn('project_id', $projectIds)
            ->with('project')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        // ---------------------------------------------------------
        // 5. NOTIFICHE E PROMEMORIA (NUOVA SEZIONE)
        // ---------------------------------------------------------
        $traUnaSettimana = Carbon::today()->addDays(7);

        // a. Task in scadenza (assegnate all'utente, scade nei prossimi 7 gg o già scaduta)
        $upcomingTasks = Task::where('assignee_id', $user->id)
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->where('due_date', '<=', $traUnaSettimana)
            ->with('project')
            ->orderBy('due_date', 'asc')
            ->get();

        // b. Pubblicazioni in scadenza (collegate ai progetti dell'utente)
        // Usiamo l'array di ID delle pubblicazioni già estratto alla riga 19
        $upcomingPublications = Publication::whereIn('id', $publications->pluck('id'))
            ->whereNotIn('status', ['published', 'accepted'])
            ->whereNotNull('target_deadline')
            ->where('target_deadline', '<=', $traUnaSettimana)
            ->orderBy('target_deadline', 'asc')
            ->get();

        // c. Milestone in scadenza
        $upcomingMilestones = Milestone::whereIn('project_id', $projectIds)
            ->whereNotIn('status', ['completed', 'done'])
            ->whereNotNull('due_date')
            ->where('due_date', '<=', $traUnaSettimana)
            ->with('project')
            ->orderBy('due_date', 'asc')
            ->get();

        // d. Totale badge notifiche
        $totaleNotifiche = $upcomingTasks->count() + $upcomingPublications->count() + $upcomingMilestones->count();

        return view('dashboard', compact(
            'user',
            'projects',
            'publications',
            'myTasks',
            'assignedTasksCount',
            'scheduledTasksCount',
            'milestones',
            'upcomingTasks',
            'upcomingPublications',
            'upcomingMilestones', // <-- ASSICURATI CHE CI SIA QUESTA RIGA!
            'totaleNotifiche'
        ));
    }
}
