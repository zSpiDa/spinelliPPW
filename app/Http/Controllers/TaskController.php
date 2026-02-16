<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // Nota: la relazione nel modello si chiama 'user', quindi usiamo 'user' qui nel with
        $tasks = Task::with(['project', 'user'])->latest()->paginate(10);
        return view('task.index', compact('tasks'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        $projects = Project::orderBy('title')->get();
        return view('task.create', compact('users', 'projects'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'status'      => 'required|in:open,in_progress,done',
            'priority'    => 'required|in:low,medium,high',

            // --- CORRETTO QUI: Usiamo assignee_id ---
            'assignee_id' => 'nullable|exists:users,id',
            'project_id'  => 'nullable|exists:projects,id',
        ]);
        // Crea la Task
        Task::create($validated);

        // Redirect intelligente
        if ($request->filled('project_id')) {
            return redirect()->route('projects.edit', $request->project_id)
                ->with('success', 'Task creata e aggiunta al progetto!');
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task creata con successo!');
    }

    // ... show, edit ...
    public function edit(Task $task)
    {
        $users = User::orderBy('name')->get();
        $projects = Project::orderBy('title')->get();
        return view('task.edit', compact('task', 'users', 'projects'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'status'      => 'required|in:open,in_progress,done',
            'priority'    => 'required|in:low,medium,high',

            // --- CORRETTO QUI: Usiamo assignee_id ---
            'assignee_id' => 'nullable|exists:users,id',
            'project_id'  => 'nullable|exists:projects,id',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Task aggiornata con successo!');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'Task eliminata.');
    }
}
