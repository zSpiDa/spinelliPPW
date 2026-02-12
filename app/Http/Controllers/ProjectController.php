<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\Tag;
use App\Models\Milestone;
use App\Models\Publication;
use App\Models\Attachment;
use App\Models\Comment;

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
            'file' => 'nullable|mimes:pdf|max:20480', // max 20MB
            'code' => 'nullable|string|max:255',
            'funder' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
            'tags.*' => 'string|max:50',
            'milestones' => 'nullable|array',
            'milestones.*' => 'string', // formato "titolo|data|status"
        ]);

        unset($validated['tags']); // Rimuove 'tags' perché non è un campo diretto di Project
        unset($validated['file']); // Rimuove 'file' perché è già gestito
        unset($validated['milestones']); // Rimuove 'milestones' perché gestito separatamente
        unset($validated['publications']); // Rimuove 'publications' perché gestito separatamente
        $project = Project::create($validated);
        
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('projects', 'public');
            $validated['file_path'] = $path;
            $project->attachments()->create([
                'path' => $path,
                'uploaded_by' => auth()->id(),
        ]);
        }

        if ($request->filled('tags')) {
            $tagNames = array_map('trim', explode(',', $request->tags));
            $tagIds = [];
            foreach ($tagNames as $name) {
                $tag = Tag::firstOrCreate(['name' => $name]);
                $tagIds[] = $tag->id;
            }
            $project->tags()->sync($tagIds);
        }
        
        // Parsing milestones da stringa "titolo|data|status, titolo|data|status"
        if ($request->filled('milestones')) {
            foreach ($request->milestones as $m) {
                $project->milestones()->create([
                    'title' => $m['title'] ?? 'Milestone senza titolo',
                    'due_date' => $m['due_date'] ?? null,
                    'status' => $m['status'] ?? 'planned',
                ]);
            }
        }

        // Parsing publications da stringa "titolo|anno, titolo|anno"
        if ($request->filled('publications')) {
            $items = array_map('trim', explode(',', $request->publications));
            foreach ($items as $item) {
                $parts = array_map('trim', explode('|', $item));
                if (count($parts) >= 2) {
                    $publication = Publication::create([
                        'title' => $parts[0],
                        'status' => $parts[1],
                        'author' => $parts[2] ?? null, // Opzionale: permette di specificare gli autori
                    ]);
                    $project->publications()->attach($publication->id);
                }
            }
        }

        return redirect('/projects')->with('success', 'Progetto creato con successo!');
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
            'code' => 'nullable|string|max:255',
            'funder' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
            'tags.*' => 'string|max:50',
        ]);
        
        unset($validated['tags']); // Rimuove 'tags' perché gestito separatamente
        unset($validated['file']); // Rimuove 'file' perché non è un campo diretto di Project
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
