<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User; // <--- AGGIUNTO: Serve per caricare la lista utenti
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['project', 'milestone', 'user'])->latest()->paginate(10);
        return view('task.index', compact('tasks'));
    }

    public function create()
    {
        return view('task.create');
    }

    public function store(Request $request)
    {
        // ...
    }

    public function show(Task $task)
    {
        return view('task.show', compact('task'));
    }

    public function edit(Task $task)
    {
        // 1. Recuperiamo tutti gli utenti per il menu a tendina "Assegnato a"
        $users = User::orderBy('name')->get();

        // 2. Passiamo sia la task che gli utenti alla vista
        return view('task.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        // 1. Validazione
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'status'      => 'required|in:open,in_progress,done',

            // --- NUOVI CAMPI ---
            'priority'    => 'required|in:low,medium,high', // Enum del DB
            'assignee_id' => 'nullable|exists:users,id',    // Deve esistere nella tabella users
        ]);

        // 2. Aggiornamento
        $task->update($validated);

        // 3. Redirect
        return redirect()->route('tasks.index')
            ->with('success', 'Task aggiornata con successo!');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')
            ->with('success', 'Task eliminata correttamente.');
    }
}
