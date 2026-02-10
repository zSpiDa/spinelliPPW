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

    public function addMember()
    {
        $projects = Project::all();
        $users = User::all();
        return view('projects.addMember', ['projects' => $projects, 'users' => $users]);
    }

    public function removeMember()
    {
        $projects = Project::all();
        $users = User::all();
        return view('projects.removeMember', ['projects' => $projects, 'users' => $users]);
    }

    public function syncMembers()
    {
        $projects = Project::all();
        $users = User::all();
        return view('projects.sync', ['projects' => $projects, 'users' => $users]);
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
        return view('projects.show', ['project' => $project]);
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
