@extends('layouts.app')

@section('content')
    <a href="{{ route('projects.index') }}" class="btn btn-link p-0 mb-3 text-decoration-none">
        ← Torna ai progetti
    </a>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="h4 mb-2">{{ $project->title }}</h2>
                    <div class="text-muted small mb-1">
                        <strong>Codice:</strong> {{ $project->code ?? 'n/d' }} |
                        <strong>Funder:</strong> {{ $project->funder ?? 'n/d' }}
                    </div>
                    <div class="text-muted small mb-2">
                        <strong>Periodo:</strong>
                        {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : '...' }}
                        →
                        {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') : '...' }}
                    </div>

                    <div class="mb-2">
                        Status: <span class="badge bg-primary">{{ $project->status ?? 'n/d' }}</span>
                    </div>

                    @if($project->tags->isNotEmpty())
                        <div class="d-flex gap-1 flex-wrap mt-2">
                            @foreach($project->tags as $t)
                                <span class="badge bg-info text-dark">#{{ $t->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning btn-sm fw-bold">
                        Modifica
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white fw-bold">
                    Milestone ({{ $project->milestones->count() }})
                </div>
                <div class="card-body">
                    @forelse($project->milestones as $m)
                        <div class="border-bottom py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">{{ $m->title }}</div>
                                <div class="text-muted small">
                                    Scadenza: {{ $m->due_date ? \Carbon\Carbon::parse($m->due_date)->format('d/m/Y') : 'n/d' }}
                                    <span class="ms-1 badge bg-light text-dark border">{{ $m->status }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small fst-italic">Nessuna milestone inserita.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white fw-bold">
                    Pubblicazioni ({{ $project->publications->count() }})
                </div>
                <div class="card-body">
                    @forelse($project->publications as $pb)
                        <div class="border-bottom py-2">
                            <div class="fw-semibold">{{ $pb->title }}</div>
                            <div class="text-muted small">Status: {{ $pb->status ?? 'n/d' }}</div>
                        </div>
                    @empty
                        <div class="text-muted small fst-italic">Nessuna pubblicazione collegata.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white fw-bold">Allegati</div>
                <div class="card-body">
                    @forelse($project->attachments as $a)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div class="text-truncate me-2">
                                {{ basename($a->path) }}
                                <div class="small text-muted">{{ $a->created_at->format('d/m/Y') }}</div>
                            </div>
                            <a href="{{ asset('storage/' . $a->path) }}" class="btn btn-sm btn-outline-primary fw-bold" download>
                                <i class="bi bi-download"></i> Scarica
                            </a>
                        </div>
                    @empty
                        <div class="text-muted small fst-italic">Nessun allegato.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white fw-bold">Commenti</div>
                <div class="card-body">
                    <form action="{{ route('projects.comments.store', $project->id) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="body" class="form-control" placeholder="Scrivi un commento..." required>
                            <button class="btn btn-primary" type="submit">Invia</button>
                        </div>
                    </form>

                    <div class="vstack gap-2" style="max-height: 200px; overflow-y: auto;">
                        @forelse($project->comments as $c)
                            <div class="bg-light p-2 rounded">
                                <div class="d-flex justify-content-between">
                                    <strong class="small">{{ optional($c->user)->name ?? 'Utente' }}</strong>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $c->created_at->format('d/m H:i') }}</span>
                                </div>
                                <div class="small">{{ $c->body }}</div>
                            </div>
                        @empty
                            <div class="text-muted small fst-italic">Nessun commento.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-5 shadow-sm border-0">
        <div class="card-header bg-white fw-bold">
            Membri del Team
        </div>
        <div class="card-body">
            @forelse($project->users as $u)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <div class="fw-bold">{{ $u->name }}</div>
                        <div class="text-muted small">{{ $u->email }}</div>
                    </div>
                    <div>
                        <span class="badge bg-secondary">
                            {{ ucfirst($u->pivot->role ?? 'Membro') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-muted small fst-italic">Nessun membro assegnato a questo progetto.</div>
            @endforelse
        </div>
    </div>

@endsection
