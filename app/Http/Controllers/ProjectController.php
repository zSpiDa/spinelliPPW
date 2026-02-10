<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['milestones','publications.authors.user','tags','attachments','comments.user']);
        $users = User::all();
        return view('projects.show', ['project' => $project, 'users' => $users]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
