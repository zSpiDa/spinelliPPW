<!-- Modifica dei dettagli di un progetto -->
@extends('layouts.app')
@section('content')
    <a href="{{ route('projects.index') }}" class="btn btn-link p-0 mb-3">← Torna alla lista dei progetti</a>
    <div class="container">
        <h1>Modifica Progetto: {{ $project->title }}</h1>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form action="{{ route('projects.update', $project) }}" method="POST" class="mb-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="title" class="form-label">Titolo del Progetto</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ $project->title }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descrizione</label>
                <textarea name="description" id="description" class="form-control">{{ $project->description }}</textarea>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Stato</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Seleziona stato</option>
                    <option value="planned" {{ $project->status === 'planned' ? 'selected' : '' }}>Planned</option>
                    <option value="ongoing" {{ $project->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="mb-3">
            <!-- Modifica della milestone con possibilità di aggiungere nuove milestone con logica -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5>Milestones</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMilestone()">
                        + Aggiungi Milestone
                    </button>
                </div>

                <div id="milestones-container">
                    {{-- Loop per le milestone esistenti --}}
                    @foreach($project->milestones as $index => $milestone)
                        <div class="card mb-2 p-3 bg-light border milestone-row">
                            {{-- ID fondamentale per l'aggiornamento. Se rimosso, il controller cancellerà la milestone --}}
                            <input type="hidden" name="milestones[{{ $index }}][id]" value="{{ $milestone->id }}">
                            
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label small text-muted">Titolo</label>
                                    <input type="text" name="milestones[{{ $index }}][title]" class="form-control" value="{{ $milestone->title }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">Scadenza</label>
                                    <input type="date" name="milestones[{{ $index }}][due_date]" class="form-control" value="{{ $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date)->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">Stato</label>
                                    <select name="milestones[{{ $index }}][status]" class="form-select">
                                        <option value="planned" {{ $milestone->status == 'planned' ? 'selected' : '' }}>Planned</option>
                                        <option value="ongoing" {{ $milestone->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                        <option value="completed" {{ $milestone->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger w-100" onclick="removeMilestone(this)">
                                        <i class="fas fa-trash"></i> X
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <script>
                // Inizializziamo il contatore basandoci sul numero attuale di milestone per evitare conflitti di indici
                let milestoneIndex = {{ $project->milestones->count() }};

                function addMilestone() {
                    const container = document.getElementById('milestones-container');
                    const newIndex = milestoneIndex++; // Incrementa l'indice per la nuova riga
                    
                    const html = `
                        <div class="card mb-2 p-3 bg-light border milestone-row">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label small text-muted">Titolo</label>
                                    <input type="text" name="milestones[new_${newIndex}][title]" class="form-control" placeholder="Nuova Milestone" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">Scadenza</label>
                                    <input type="date" name="milestones[new_${newIndex}][due_date]" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">Stato</label>
                                    <select name="milestones[new_${newIndex}][status]" class="form-select">
                                        <option value="planned" selected>Planned</option>
                                        <option value="ongoing">Ongoing</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger w-100" onclick="removeMilestone(this)">
                                        X
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Aggiunge l'HTML al contenitore
                    container.insertAdjacentHTML('beforeend', html);
                }

                function removeMilestone(button) {
                    // Rimuove semplicemente l'elemento dal DOM.
                    // Se l'elemento aveva un ID nascosto, non verrà inviato al server.
                    // La logica "whereNotIn" del controller cancellerà automaticamente la milestone dal database.
                    button.closest('.milestone-row').remove();
                }
            </script>
            <!-- Upload allegati per documenti associati al progetto e rimozione dei file associati-->
            <div class="mb-3">
            <h5>Allegati</h5>
                @if($project->attachments->count() > 0)
                    <label class="form-label text-muted small">Allegati esistenti (Seleziona per rimuovere):</label>
                    <ul class="list-group mb-3">
                        @foreach($project->attachments as $attachment)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{-- Link per scaricare/vedere il file --}}
                                <a href="{{ Storage::url($attachment->path) }}" target="_blank" class="text-decoration-none">
                                    <i class="fas fa-file-pdf text-danger"></i> 
                                    {{ $attachment->name ?? basename($attachment->path) }}
                                </a>
                                
                                {{-- Checkbox per cancellare --}}
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}" id="del_att_{{ $attachment->id }}">
                                    <label class="form-check-label text-danger" for="del_att_{{ $attachment->id }}">Rimuovi</label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted small">Nessun allegato presente.</p>
                @endif
                <div class="mb-2">
                    <label for="file" class="form-label">Aggiungi nuovo allegato (PDF)</label>
                    {{-- Nota: Se vuoi caricare più file insieme aggiungi 'multiple' e cambia il nome in 'files[]' --}}
                    <input type="file" name="file" id="file" class="form-control" accept=".pdf">
                </div>
            </div>
            <!-- Sezione per la creazione rapida di task associati al progetto -->
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
            
            <button type="submit" class="btn btn-primary">Salva Modifiche</button>
        </form>
    </div>
@endsection