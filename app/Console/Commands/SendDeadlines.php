<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Task;
use App\Models\Milestone;
use App\Models\Publication;
use App\Notifications\DeadlineReminder;
use Carbon\Carbon;

class SendDeadlines extends Command
{
    // Il nome del comando da lanciare nel terminale
    protected $signature = 'app:send-deadlines';
    protected $description = 'Invia e-mail giornaliere per le scadenze imminenti';

    public function handle()
    {
        $users = User::all();
        $traUnaSettimana = Carbon::today()->addDays(7);

        foreach ($users as $user) {
            // 1. Conta le Task dell'utente in scadenza
            $tasksCount = Task::where('assignee_id', $user->id)
                ->where('status', '!=', 'done')
                ->whereNotNull('due_date')
                ->where('due_date', '<=', $traUnaSettimana)
                ->count();

            // Ottieni gli ID dei progetti dell'utente
            $projectIds = $user->projects()->pluck('projects.id');

            // 2. Conta le Milestone in scadenza per quei progetti
            $milestonesCount = Milestone::whereIn('project_id', $projectIds)
                ->whereNotIn('status', ['completed', 'done'])
                ->whereNotNull('due_date')
                ->where('due_date', '<=', $traUnaSettimana)
                ->count();

            // 3. Conta le Pubblicazioni in scadenza per quei progetti
            $pubsCount = Publication::whereHas('projects', function($q) use ($projectIds) {
                $q->whereIn('projects.id', $projectIds);
            })
                ->whereNotIn('status', ['published', 'accepted'])
                ->whereNotNull('target_deadline')
                ->where('target_deadline', '<=', $traUnaSettimana)
                ->count();

            $totale = $tasksCount + $milestonesCount + $pubsCount;

            // Se c'è almeno 1 scadenza, invia l'email
            if ($totale > 0) {
                $user->notify(new DeadlineReminder($totale));
            }
        }

        $this->info('Email di promemoria scadenze inviate con successo!');
    }
}
