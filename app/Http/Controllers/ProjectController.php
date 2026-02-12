<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\Tag;
use App\Models\Milestone;
use App\Models\Publication;
use App\Models\Attachment;
use App\Models\Comment; // Se lo usi
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['milestones', 'tags', 'publications'])
            ->orderBy('title')
            ->get();

        return view('projects.index', ['projects' => $projects]);
    }

    /**
     * Mostra la singola task/progetto e passa gli utenti per il form "Aggiungi Membro"
     */
    public function show(Project $project)
    {
        // Carica le relazioni necessarie
        $project->load(['milestones', 'publications.authors', 'tags', 'attachments', 'comments.user', 'users']);

        // Recupera TUTTI gli utenti ordinati per nome (per il menu a tendina)
        $users = User::orderBy('name')->get();

        return view('projects.show', ['project' => $project, 'users' => $users]);
    }

    /**
     * Aggiunge un membro al team del progetto
     */
    public function addMember(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role'    => ['required', 'in:pi,manager,researcher,collaborator'],
        ]);

        // syncWithoutDetaching evita di cancellare altri membri o duplicare lo stesso
        $project->users()->syncWithoutDetaching([
            $request->user_id => [
                'role'   => $request->role,
                'effort' => $request->effort ?? null, // Se hai un campo effort nella pivot
            ],
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Membro aggiunto al team.');
    }

    /**
     * Rimuove un membro dal team
     */
    public function removeMember(Project $project, User $user)
    {
        $project->users()->detach($user->id);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Membro rimosso dal team.');
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'status'      => 'nullable|string|max:100',
            'file'        => 'nullable|mimes:pdf|max:20480', // max 20MB
            'code'        => 'nullable|string|max:255',
            'funder'      => 'nullable|string|max:255',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',

            // Validazione campi complessi
            'tags'         => 'nullable|string',
            'milestones'   => 'nullable|array',
            'publications' => 'nullable|string',
        ]);

        // Rimuoviamo i campi che non vanno direttamente nella tabella projects
        $tagsInput = $validated['tags'] ?? null;
        unset($validated['tags']);
        unset($validated['file']);
        unset($validated['milestones']);
        unset($validated['publications']);

        // 1. Crea il Progetto
        $project = Project::create($validated);

        // 2. Gestione File Allegato
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('projects', 'public');

            $project->attachments()->create([
                'path' => $path,
                'name' => $request->file('file')->getClientOriginalName(), // Opzionale: salva nome originale
                'uploaded_by' => auth()->id(),
            ]);
        }

        // 3. Gestione Tags (Crea se non esistono e collega)
        if ($tagsInput) {
            $tagNames = array_map('trim', explode(',', $tagsInput));
            $tagIds = [];
            foreach ($tagNames as $name) {
                if(!empty($name)){
                    $tag = Tag::firstOrCreate(['name' => $name]);
                    $tagIds[] = $tag->id;
                }
            }
            $project->tags()->sync($tagIds);
        }

        // 4. Gestione Milestones (da array)
        if ($request->filled('milestones')) {
            foreach ($request->milestones as $m) {
                // Assicurati che $m sia un array con le chiavi giuste
                if(is_array($m)) {
                    $project->milestones()->create([
                        'title'    => $m['title'] ?? 'Milestone',
                        'due_date' => $m['due_date'] ?? null,
                        'status'   => $m['status'] ?? 'planned',
                    ]);
                }
            }
        }

        // 5. Gestione Pubblicazioni (Parsing stringa custom "titolo|status|autore")
        if ($request->filled('publications')) {
            $items = array_map('trim', explode(',', $request->publications));
            foreach ($items as $item) {
                $parts = array_map('trim', explode('|', $item));
                if (count($parts) >= 1 && !empty($parts[0])) {
                    $publication = Publication::create([
                        'title'  => $parts[0],
                        'status' => $parts[1] ?? 'published',
                        // Nota: 'author' qui è una stringa libera, non una relazione User
                        'author' => $parts[2] ?? null,
                    ]);
                    $project->publications()->attach($publication->id);
                }
            }
        }

        return redirect()->route('projects.index')
            ->with('success', 'Progetto creato con successo!');
    }

    public function edit(Project $project)
    {
        // Recuperiamo gli utenti per popolare la select nella pagina di modifica
        $users = User::orderBy('name')->get();

        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'status'      => 'nullable|string|max:100',
            'code'        => 'nullable|string|max:255',
            'funder'      => 'nullable|string|max:255',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'tags'        => 'nullable|string', // Stringa separata da virgola
        ]);

        // Salviamo i tag in una variabile a parte
        $tagsInput = $validated['tags'] ?? null;

        // Rimuoviamo 'tags' dall'array per l'update del modello Project
        unset($validated['tags']);

        // 1. Aggiorna dati base
        $project->update($validated);

        // 2. AGGIUNTO: Aggiornamento Tags (Logica mancante nel tuo codice precedente)
        if ($tagsInput !== null) {
            $tagNames = array_map('trim', explode(',', $tagsInput));
            $tagIds = [];
            foreach ($tagNames as $name) {
                if(!empty($name)) {
                    $tag = Tag::firstOrCreate(['name' => $name]);
                    $tagIds[] = $tag->id;
                }
            }
            // Sync aggiorna le relazioni: rimuove quelle vecchie non presenti e aggiunge le nuove
            $project->tags()->sync($tagIds);
        } else {
            // Se il campo tags è vuoto o null, rimuovi tutti i tag associati
            // (Attento: abilita questa riga solo se vuoi che un campo vuoto cancelli i tag)
            // $project->tags()->detach();
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Progetto aggiornato correttamente.');
    }

    public function destroy(Project $project)
    {
        // Opzionale: Elimina allegati fisici se necessario
        foreach($project->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->path);
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Progetto eliminato.');
    }

    // Funzione helper per output HTML rapido (se ti serve per debug/API semplici)
    public function html()
    {
        $projects = Project::orderBy('title')->get(['title','status']);
        $out = '<h2>Progetti</h2><ul>';
        foreach($projects as $p){
            $out .= '<li><strong>'.e($p->title).'</strong> ('.e($p->status ?? 'n/d').')</li>';
        }
        $out .= '</ul>';
        return $out;
    }
}
