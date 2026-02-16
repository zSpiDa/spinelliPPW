<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    /**
     * Salva una nuova milestone collegata a un progetto.
     */
    public function store(Request $request, Project $project)
    {
        // 1. Validazione
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:planned,ongoing,completed', // Se vuoi permettere di specificare lo status al momento della creazione
            // Se non passi lo status, di default mettiamo 'planned' nel database o nel model
        ]);

        // 2. Creazione (sfruttando la relazione)
        $project->milestones()->create([
            'title' => $validated['title'],
            'due_date' => $validated['due_date'],
            'status' => $validated['status'] ?? 'planned', // Impostiamo lo stato iniziale di default se non fornito
        ]);

        // 3. Ritorno alla pagina precedente
        return back()->with('success', 'Milestone aggiunta con successo!');
    }

    /**
     * Aggiorna una milestone esistente.
     */

    public function edit(Milestone $milestone)
    {
        // Carica la vista 'milestones.edit' passando i dati della milestone
        return view('milestones.edit', compact('milestone'));
    }

    public function update(Request $request, Milestone $milestone)
    {
        // 1. Validazione
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'status' => 'required|in:planned,ongoing,completed',
        ]);

        // 2. Aggiornamento
        $milestone->update($validated);

        // 3. Ritorno alla pagina precedente
        return redirect()->route('projects.show', $milestone->project_id)
            ->with('success', 'Milestone aggiornata con successo!');
    }

    /**
     * Elimina una milestone.
     */
    public function destroy(Milestone $milestone)
    {
        $milestone->delete();
        return back()->with('success', 'Milestone eliminata.');
    }
}
