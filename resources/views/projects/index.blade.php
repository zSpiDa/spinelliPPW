<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Elenco Progetti</title>
    <style>body{font-family:Segoe UI,Arial,Helvetica,sans-serif;margin:20px}h1{margin-bottom:10px}article{border:1px solid #ddd;padding:12px;margin-bottom:12px;border-radius:6px}ul{margin:6px 0 12px 20px}</style>
</head>
<body>
    <h1>Progetti</h1>

    @forelse($projects as $project)
        <article>
            <h2>{{ $project->title }} <small>({{ $project->code }})</small></h2>
            <p><strong>Funder:</strong> {{ $project->funder ?? 'n/d' }} | <strong>Status:</strong> {{ $project->status ?? 'n/d' }}</p>
            <p>{{ $project->description }}</p>

            <h4>Utenti</h4>
            @if($project->users->isNotEmpty())
                <ul>
                    @foreach($project->users as $user)
                        <li>{{ $user->name }} &lt;{{ $user->email }}&gt; - ruolo: {{ $user->pivot->role ?? '-' }} (effort: {{ $user->pivot->effort ?? 0 }})</li>
                    @endforeach
                </ul>
            @else
                <p>Nessun utente assegnato</p>
            @endif

            <h4>Milestones</h4>
            @if($project->milestones->isNotEmpty())
                <ul>
                    @foreach($project->milestones as $m)
                        <li>{{ $m->title }} - stato: {{ $m->status ?? 'n/d' }} - scadenza: {{ $m->due_date ?? 'n/d' }}</li>
                    @endforeach
                </ul>
            @else
                <p>Nessuna milestone</p>
            @endif

            <h4>Publications</h4>
            @if($project->publications->isNotEmpty())
                <ul>
                    @foreach($project->publications as $pub)
                        <li>{{ $pub->title }} @if($pub->status) ({{ $pub->status }}) @endif</li>
                    @endforeach
                </ul>
            @else
                <p>Nessuna pubblicazione</p>
            @endif

            <h4>Allegati</h4>
            @if($project->attachments->isNotEmpty())
                <ul>
                    @foreach($project->attachments as $att)
                        <li>{{ $att->path }} (uploaded_by: {{ $att->uploaded_by }})</li>
                    @endforeach
                </ul>
            @else
                <p>Nessun allegato</p>
            @endif

            <h4>Commenti</h4>
            @if($project->comments->isNotEmpty())
                <ul>
                    @foreach($project->comments as $c)
                        <li>{{ $c->user?->name ?? 'Anonimo' }}: {{ $c->body }}</li>
                    @endforeach
                </ul>
            @else
                <p>Nessun commento</p>
            @endif

            <h4>Tasks</h4>
            @if($project->tasks->isNotEmpty())
                <ul>
                    @foreach($project->tasks as $t)
                        <li>{{ $t->title }} - assegnato a: {{ $t->assignee?->name ?? 'n/d' }} - stato: {{ $t->status ?? 'n/d' }}</li>
                    @endforeach
                </ul>
            @else
                <p>Nessun task</p>
            @endif

        </article>
    @empty
        <p>Non ci sono progetti disponibili.</p>
    @endforelse

</body>
</html>
<!-- resources/views/projects/index.blade.php -->
@extends('layouts.app')

@section('content')
    <h2 class="h5 mb-3">Progetti di Ricerca</h2>

    @forelse ($projects as $p)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="h6 m-0">{{ $p->title }}</h3>
                        <div class="text-muted small">Status: {{ $p->status ?? 'n/d' }}</div>
                    </div>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('projects.show', $p->id) }}">Dettagli</a>
                </div>

                @if($p->tags->isNotEmpty())
                    <div class="mt-2 d-flex gap-2 flex-wrap">
                        @foreach($p->tags as $t)
                            <span class="badge text-bg-info">#{{ $t->name }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="mt-2 text-muted small">
                    Milestone: {{ $p->milestones->count() }} |
                    Pubblicazioni collegate: {{ $p->publications->count() }}
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary">Nessun progetto presente.</div>
    @endforelse
@endsection
