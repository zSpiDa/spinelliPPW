@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card mb-4">
                    <div class="card-header fw-bold">
                        Modifica Progetto: {{ $project->title }}
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('projects.update', $project) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="title" class="form-label">Titolo</label>
                                <input type="text"
                                       class="form-control"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', $project->title) }}"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Stato</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" {{ $project->status == 'active' ? 'selected' : '' }}>Attivo</option>
                                    <option value="ongoing" {{ $project->status == 'ongoing' ? 'selected' : '' }}>In corso</option>
                                    <option value="draft" {{ $project->status == 'draft' ? 'selected' : '' }}>Archiviato</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">Codice</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $project->code) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="funder" class="form-label">Funder</label>
                                    <input type="text" class="form-control" id="funder" name="funder" value="{{ old('funder', $project->funder) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label">Data inizio</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $project->start_date) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">Data fine</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $project->end_date) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descrizione</label>
                                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $project->description) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="milestones" class="form-label">Milestones (formato: titolo|data|stato)</label>
                                <input type="text" class="form-control" id="milestones" name="milestones" value="{{ old('milestones') }}" placeholder="es: M1|2024-01-01|planned">
                            </div>
                            <div class="mb-3">
                                <label for="publications" class="form-label">Publications (formato: titolo|anno)</label>
                                <input type="text" class="form-control" id="publications" name="publications" value="{{ old('publications') }}" placeholder="es: Articolo 1|2024">
                            </div>

                            <div class="mb-3">
                                <label for="tags" class="form-label">Tags (separati da virgola)</label>
                                <input type="text"
                                       class="form-control"
                                       id="tags"
                                       name="tags"
                                       value="{{ old('tags', $project->tags->pluck('name')->implode(', ')) }}">
                            </div>

                            <div class="mb-3">
                                <label for="file" class="form-label">Allega file (PDF)</label>
                                <input type="file" class="form-control" id="file" name="file" accept=".pdf">
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Indietro</a>
                                <button type="submit" class="btn btn-primary">Salva Dati Progetto</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header fw-bold">
                        👥 Gestione Membri del Team
                    </div>
                    <div class="card-body">

                        <h6 class="fw-bold mb-3">Membri Attuali</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Ruolo</th>
                                    <th class="text-end">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($project->users as $u)
                                    <tr>
                                        <td>
                                            {{ $u->name }} <br>
                                            <span class="text-muted small">{{ $u->email }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($u->pivot->role ?? 'membro') }}</span>
                                        </td>
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('projects.removeMember', [$project, $u]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Rimuovere {{ $u->name }} dal team?')">
                                                    Rimuovi
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">Nessun membro nel team.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <h6 class="fw-bold mb-3 text-primary">➕ Aggiungi Nuovo Membro</h6>
                        <form method="POST" action="{{ route('projects.addMember', $project) }}">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small text-muted">Utente</label>
                                    <select class="form-select" name="user_id" required>
                                        <option value="">-- Seleziona --</option>
                                        @foreach($users as $u)
                                            @php $isMember = $project->users->contains($u->id); @endphp
                                            <option value="{{ $u->id }}" {{ $isMember ? 'disabled' : '' }}>
                                                {{ $u->name }} {{ $isMember ? '(Già presente)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Ruolo</label>
                                    <select class="form-select" name="role" required>
                                        <option value="collaborator">Collaborator</option>
                                        <option value="researcher">Researcher</option>
                                        <option value="manager">Project Manager</option>
                                        <option value="pi">Principal Investigator (PI)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-outline-primary w-100">Aggiungi</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mb-5">
                    <div class="card-header fw-bold">
                        Crea Nuova Task
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('tasks.store') }}">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $project->id }}">

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Titolo Task</label>
                                    <input type="text" name="title" class="form-control" placeholder="Es: Analisi dati preliminari..." required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Assegna a</label>
                                    <select name="assignee_id" class="form-select">
                                        <option value="">-- Nessuno --</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Stato</label>
                                    <select name="status" class="form-select">
                                        <option value="open" selected>Da Fare</option>
                                        <option value="in_progress">In Corso</option>
                                        <option value="done">Completato</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Priorità</label>
                                    <select name="priority" class="form-select">
                                        <option value="low">Bassa</option>
                                        <option value="medium" selected>Media</option>
                                        <option value="high">Alta</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Scadenza</label>
                                    <input type="date" name="due_date" class="form-control">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Descrizione (opzionale)</label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="Dettagli aggiuntivi..."></textarea>
                                </div>

                                <div class="col-md-12 text-end mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg"></i> Crea Task
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
