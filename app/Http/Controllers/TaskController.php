<?php

namespace App\Http\Controllers;

use App\Models\Task; // Assicurati che questa riga ci sia!
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Carica le task con i dati collegati
        $tasks = Task::with(['project', 'milestone', 'user'])->latest()->paginate(10);

        // MODIFICA QUI: Cartella 'task' (singolare)
        return view('task.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Solitamente le task si creano dai progetti, ma se serve una pagina dedicata:
        return view('task.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Logica salvataggio...
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return view('task.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        // MODIFICA QUI: Punta alla cartella 'task' (singolare)
        // Laravel inietterà automaticamente la $task grazie al Route Model Binding
        return view('task.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // 1. Validazione
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            // Accetta i tuoi stati standard
            'status' => 'required|in:todo,open,in_progress,ongoing,completed,done',
        ]);

        // 2. Aggiornamento nel DB
        $task->update($validated);

        // 3. Redirect (Nota: le rotte restano plurali 'tasks.index' se definite come resource('tasks'))
        return redirect()->route('tasks.index')
            ->with('success', 'Task aggiornata con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task eliminata correttamente.');
    }
}
