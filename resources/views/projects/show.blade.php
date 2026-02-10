<!-- resources/views/projects/show.blade.php -->
@extends('layouts.app')

@section('content')
    <a href="{{ route('projects.index') }}" class="btn btn-link p-0 mb-3">← Torna ai progetti</a>

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h5">{{ $project->title }}</h2>
            <div class="text-muted small">Codice: {{ $project->code ?? 'n/d' }}</div>
            <div class="text-muted small">Funder: {{ $project->funder ?? 'n/d' }}</div>
            <div class="text-muted small">Periodo: {{ $project->start_date ?? 'n/d' }} → {{ $project->end_date ?? 'n/d' }}</div>
            <div class="mt-2">Status: <span class="badge text-bg-primary">{{ $project->status ?? 'n/d' }}</span></div>

            @if($project->tags->isNotEmpty())
                <div class="mt-2 d-flex gap-2 flex-wrap">
                    @foreach($project->tags as $t)
                        <span class="badge text-bg-info">#{{ $t->name }}</span>
                    @endforeach
                </div>
            @endif

            @can('delete-project', $project)


                @csrf @method('DELETE')
                Elimina


            @endcan
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="h6">Milestone ({{ $project->milestones->count() }})</h3>
                    @forelse($project->milestones as $m)
                        <div class="border-bottom py-2">
                            <div class="fw-semibold">{{ $m->title }}</div>
                            <div class="text-muted small">Scadenza: {{ $m->due_date ?? 'n/d' }} | Stato: {{ $m->status ?? 'n/d' }}</div>
                        </div>
                    @empty
                        <div class="text-muted">Nessuna milestone.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="h6">Pubblicazioni collegate ({{ $project->publications->count() }})</h3>
                    @forelse($project->publications as $pb)
                        <div class="border-bottom py-2">
                            <div class="fw-semibold">{{ $pb->title }}</div>
                            <div class="text-muted small">Stato: {{ $pb->status ?? 'n/d' }}</div>

                            @if($pb->authors->isNotEmpty())
                                <div class="small">
                                    Autori:
                                    @foreach($pb->authors as $a)
                                        <span>{{ optional($a->user)->name ?? 'autore n/d' }}@if(!$loop->last), @endif</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-muted">Nessuna pubblicazione.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="h6">Allegati</h3>
                    @forelse($project->attachments as $att)
                        <div class="small">{{ $att->path }} (uploader #{{ $att->uploaded_by }})</div>
                    @empty
                        <div class="text-muted">Nessun allegato.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="h6">Commenti</h3>
                    @forelse($project->comments as $c)
                        <div class="border-bottom py-2">
                            <div class="small"><strong>{{ optional($c->user)->name ?? 'utente n/d' }}:</strong> {{ $c->body }}</div>
                        </div>
                    @empty
                        <div class="text-muted">Nessun commento.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- aggiunta e rimozione membri (solo per PI e Manager) con form di ricerca utenti e selezione ruolo (collaboratore, manager, pi, researcher) -->
        <div>
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="h6">Membri del progetto</h3>
                    @forelse($project->users as $u)
                        <div class="border-bottom py-2 d-flex justify-content-between align-items-center">
                            <div class="small"><strong>{{ $u->name }}</strong> ({{ $u->pivot->role ?? 'n/d' }})</div>
                            <form method="POST" action="{{ route('projects.removeMember', [$project, $u]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Rimuovere questo membro?')">
                                    Rimuovi
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-muted">Nessun membro assegnato.</div>
                    @endforelse
                </div>
            <!-- form di ricerca utenti e selezione ruolo (collaboratore, manager, pi, researcher) -->
            <div class="card-footer">
                <h3 class="h6">Aggiungi membro</h3>
                <form method="POST" action="{{ route('projects.addMember', $project) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Seleziona utente</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">-- Seleziona utente --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Seleziona ruolo</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">-- Seleziona ruolo --</option>
                            <option value="pi">PI</option>
                            <option value="manager">Project Manager</option>
                            <option value="researcher">Researcher</option>
                            <option value="collaborator">Collaborator</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Aggiungi membro</button>
                </form>
            </div>
        </div>
    </div>
@endsection
