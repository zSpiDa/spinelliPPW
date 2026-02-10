<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;

class ProjectController extends Controller
{

    public function index()
    {
        $projects = Project::with(['milestones','tags','publications'])->orderBy('title')->get();
        return view('projects.index', ['projects' => $projects]);
    }

    public function addMember(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role'    => ['required', 'in:pi,manager,researcher,collaborator'],
        ]);

        $project->users()->syncWithoutDetaching([
            $request->user_id => [
                'role'   => $request->role,
                'effort' => $request->effort ?? null,
            ],
        ]);

        return redirect()->route('projects.show', $project)->with('success', 'Membro aggiunto.');
    }

    public function removeMember(Project $project, User $user)
    {
        $project->users()->detach($user->id);

        return redirect()->route('projects.show', $project)->with('success', 'Membro rimosso.');
    }

    public function create()
    {
        return view('projects.create');

    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'  => 'required|string|max:255',
            'status' => 'nullable|string|max:100',
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Progetto creato con successo');
    }


    public function show(Project $project)
    {
        $project->load(['milestones','publications.authors.user','tags','attachments','comments.user']);
        $users = User::all();
        return view('projects.show', ['project' => $project, 'users' => $users]);
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }


    //Aggiorna Progetto
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'  => 'required|string|max:255',
            'status' => 'nullable|string|max:100',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Progetto aggiornato');
    }


    //Elimina Progetto
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Progetto eliminato');
    }


    public function html()
    {
        $projects = \App\Models\Project::orderBy('title')->get(['title','status']);
        $out = '<h2>Progetti</h2><ul>';
        foreach($projects as $p){ $out .= '<li><strong>'.$p->title.'</strong> ('.($p->status ?? 'n/d').')</li>'; }
        $out .= '</ul>';
        return $out;
    }
}
