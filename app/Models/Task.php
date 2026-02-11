<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'milestone_id', // Aggiunto: serve per salvare il collegamento
        'user_id',      // Modificato da assignee_id a user_id per standard (vedi nota sotto)
        'title',
        'description',
        'due_date',
        'status',
        'priority'
    ];

    // Relazione: Un task appartiene a un Progetto
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relazione: Un task può appartenere a una Milestone (QUELLA CHE MANCAVA)
    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    // Relazione: Un task è assegnato a un Utente
    // Rinominata da 'assignee' a 'user' per compatibilità col Controller
    public function user()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
