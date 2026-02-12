<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'milestone_id',
        'assignee_id',  // <--- CORRETTO! (Prima era user_id)
        'title',
        'description',
        'due_date',
        'status',
        'priority'      // Assicurati che questo ci sia per la priorità
    ];

    // Relazione: Un task appartiene a un Progetto
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relazione: Un task può appartenere a una Milestone
    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    // Relazione: Un task è assegnato a un Utente
    public function user()
    {
        // Specifichiamo 'assignee_id' perché è il nome della colonna nel DB
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
