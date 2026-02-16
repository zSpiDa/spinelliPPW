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
use App\Models\Task; // Se vuoi creare task direttamente da qui

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['milestones', 'tags', 'publications', 'users'])
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
            'tasks'        => 'nullable|array',
        ]);

        // Rimuoviamo i campi che non vanno direttamente nella tabella projects
        $tagsInput = $validated['tags'] ?? null;
        unset($validated['tags']);
        unset($validated['file']);
        unset($validated['milestones']);
        unset($validated['publications']);
        unset($validated['tasks']);
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
            'milestones'   => 'nullable|array',
            'file'        => 'nullable|mimes:pdf|max:20480', // max 20MB
            'tasks'        => 'nullable|array',
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

        // Recuperiamo gli ID delle milestone presenti nel form
        $sentIds = collect($request->input('milestones', []))
            ->pluck('id')
            ->filter() // Filtra valori null/vuoti (che appartengono alle nuove milestone)
            ->toArray();

        // Eliminiamo dal DB tutte le milestone di questo progetto che NON sono nella lista inviata
        $project->milestones()
            ->whereNotIn('id', $sentIds)
            ->delete();

        // 2. GESTIONE AGGIORNAMENTO E CREAZIONE
        if ($request->has('milestones')) {
            foreach ($request->milestones as $m) {
                if (isset($m['id']) && $m['id']) {
            // AGGIORNAMENTO: Cerchiamo la milestone SOLO all'interno di questo progetto per sicurezza
            $milestone = $project->milestones()->find($m['id']);
            
            if ($milestone) {
                $milestone->update([
                    'title'    => $m['title'],
                    'due_date' => $m['due_date'],
                    'status'   => $m['status'],
                ]);
            }
        } else {
            // CREAZIONE: Se non c'è ID, è una nuova milestone
            $project->milestones()->create([
                'title'    => $m['title'],
                'due_date' => $m['due_date'],
                'status'   => $m['status'] ?? 'planned',
            ]);
        }
    }
    // 3. Gestione molteplici file (se vuoi permettere di aggiungere nuovi allegati durante l'update) ed anche rimozione dei file esistenti
    if ($request->hasFile('file')) {
        $path = $request->file('file')->store('projects', 'public');

        $project->attachments()->create([
            'path' => $path,
            'name' => $request->file('file')->getClientOriginalName(),
            'uploaded_by' => auth()->id(),
        ]);
    }
    else {
        // Se vuoi permettere la rimozione degli allegati esistenti, potresti aggiungere un campo checkbox per ogni allegato nella vista edit.blade.php
        // e qui controllare se è stato richiesto di eliminare qualche file
        // Esempio (assumendo che tu abbia un array di ID degli allegati da eliminare):
        if ($request->filled('delete_attachments')) {
        // Recuperiamo gli ID inviati dal form
        $idsToDelete = $request->input('delete_attachments');
        
        // Cerchiamo SOLO tra gli allegati di QUESTO progetto (Sicurezza)
        $attachmentsToDelete = $project->attachments()->whereIn('id', $idsToDelete)->get();

        foreach ($attachmentsToDelete as $attachment) {
            // 1. Elimina il file fisico dallo storage
            if (Storage::disk('public')->exists($attachment->path)) {
                Storage::disk('public')->delete($attachment->path);
            }
            
            // 2. Elimina il record dal database
            $attachment->delete();
        }
    }
    }
    
    //rimozione e aggiunta tags
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
    }
    
    unset($validated['tasks']);
    //Aggiunta tasks
    if ($request->has('tasks')) {
        foreach ($request->tasks as $t) {
            // Assicurati che $t sia un array con le chiavi giuste
            if(is_array($t)) {
                $project->tasks()->create([
                    'title'       => $t['title'] ?? 'Task',
                    'description' => $t['description'] ?? null,
                    'due_date'    => $t['due_date'] ?? null,
                    'status'      => $t['status'] ?? 'open',
                    'priority'    => $t['priority'] ?? 'medium',
                ]);
            }
        }
    }

    return redirect()->route('projects.show', $project)
            ->with('success', 'Progetto aggiornato correttamente.');
    }
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
