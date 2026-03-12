@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <a href="{{ route('projects.index') }}" class="btn btn-link p-0 mb-3">← Torna alla lista dei progetti</a>

        <h1 class="mb-4">Crea nuovo progetto</h1>

        <form action="{{ route('projects.store') }}" enctype="multipart/form-data" method="POST" class="mb-4">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-3">
                <label for="title" class="form-label fw-bold">Titolo del Progetto</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-bold">Descrizione</label>
                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label fw-bold">Stato</label>
                <select class="form-select" id="status" name="status">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="code" class="form-label fw-bold">Codice</label>
                <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}">
            </div>

            <div class="mb-3">
                <label for="funder" class="form-label fw-bold">Funder</label>
                <input type="text" class="form-control" id="funder" name="funder" value="{{ old('funder') }}">
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_date" class="form-label fw-bold">Data inizio</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}">
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label fw-bold">Data fine</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}">
                </div>
            </div>

            {{-- ----------------------------------------------------- --}}
            {{-- SEZIONE: MILESTONE DINAMICHE                          --}}
            {{-- ----------------------------------------------------- --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold m-0">Milestones</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMilestone()">
                        + Aggiungi Milestone
                    </button>
                </div>

                <div id="milestones-container"></div>

                <script>
                    let milestoneIndex = 0;
                    function addMilestone() {
                        const container = document.getElementById('milestones-container');
                        const newIndex = milestoneIndex++;

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
                                            <option value="planned" selected>Pianificato</option>
                                            <option value="ongoing">In Corso</option>
                                            <option value="completed">Completato</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.milestone-row').remove()">
                                            X
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', html);
                    }
                </script>
            </div>

            {{-- ----------------------------------------------------- --}}
            {{-- SEZIONE: TASK DINAMICHE (Nuova implementazione)       --}}
            {{-- ----------------------------------------------------- --}}
            <div class="card mb-5 border-0 shadow-sm">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                    Crea Task Iniziali per il Progetto
                    <button type="button" class="btn btn-sm btn-primary" onclick="addTask()">
                        + Aggiungi Task
                    </button>
                </div>
                <div class="card-body bg-light" id="tasks-container">
                    <p class="text-muted small mb-0" id="tasks-placeholder">Nessuna task aggiunta. Clicca su "+ Aggiungi Task" per crearne una.</p>
                </div>
            </div>

            <script>
                let taskIndex = 0;
                // Pre-renderizziamo gli utenti per i menu a tendina delle task
                const taskUsersOptions = `@foreach($users as $u)<option value="{{ $u->id }}">{{ str_replace("'", "\'", $u->name) }}</option>@endforeach`;

                function addTask() {
                    // Rimuovi il placeholder se è la prima task
                    const placeholder = document.getElementById('tasks-placeholder');
                    if(placeholder) placeholder.style.display = 'none';

                    const container = document.getElementById('tasks-container');
                    const newIndex = taskIndex++;

                    const html = `
                        <div class="card mb-3 p-3 bg-white border task-row">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Titolo Task</label>
                                    <input type="text" name="tasks[new_${newIndex}][title]" class="form-control" placeholder="Es: Analisi dati preliminari..." required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Assegna a</label>
                                    <select name="tasks[new_${newIndex}][assignee_id]" class="form-select">
                                        <option value="">-- Nessuno --</option>
                                        ${taskUsersOptions}
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Stato</label>
                                    <select name="tasks[new_${newIndex}][status]" class="form-select">
                                        <option value="open" selected>Da Fare</option>
                                        <option value="in_progress">In Corso</option>
                                        <option value="done">Completato</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Priorità</label>
                                    <select name="tasks[new_${newIndex}][priority]" class="form-select">
                                        <option value="low">Bassa</option>
                                        <option value="medium" selected>Media</option>
                                        <option value="high">Alta</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Scadenza</label>
                                    <input type="date" name="tasks[new_${newIndex}][due_date]" class="form-control">
                                </div>

                                <div class="col-md-11">
                                    <label class="form-label">Descrizione (opzionale)</label>
                                    <textarea name="tasks[new_${newIndex}][description]" class="form-control" rows="1" placeholder="Dettagli aggiuntivi..."></textarea>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger w-100" onclick="removeTask(this)">
                                        X
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                }

                function removeTask(button) {
                    button.closest('.task-row').remove();
                    // Se non ci sono più task, rimostra il placeholder
                    if(document.querySelectorAll('.task-row').length === 0) {
                        document.getElementById('tasks-placeholder').style.display = 'block';
                    }
                }
            </script>


            {{-- ----------------------------------------------------- --}}
            {{-- ALLEGATI E MEMBRI                                     --}}
            {{-- ----------------------------------------------------- --}}
            <div class="mb-3">
                <h5 class="fw-bold">Allegato</h5>
                <div class="mb-2">
                    <label for="file" class="form-label">Aggiungi allegato (PDF)</label>
                    <input type="file" name="file" id="file" class="form-control" accept=".pdf">
                </div>
            </div>

            <div class="mb-3">
                <h5 class="fw-bold">Membri del Progetto</h5>
                <div id="members-container" class="mb-3">
                    @if(old('users'))
                        @foreach($users as $user)
                            @if(in_array($user->id, old('users')))
                                <div class="d-flex justify-content-between align-items-center border p-2 mb-2 bg-white rounded member-row">
                                    <span>{{ $user->name }} <small class="text-muted">({{ $user->role ?? 'Utente' }})</small></span>
                                    <input type="hidden" name="users[]" value="{{ $user->id }}">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.member-row').remove()">Rimuovi</button>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="input-group">
                    <select id="user-select" class="form-select">
                        <option value="">-- Seleziona utente da aggiungere --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-primary" onclick="addMember()">Aggiungi Membro</button>
                </div>
            </div>

            <script>
                function addMember() {
                    const select = document.getElementById('user-select');
                    const userId = select.value;
                    const userName = select.options[select.selectedIndex].text;

                    if (!userId) return;

                    if (document.querySelector(`input[name="users[]"][value="${userId}"]`)) {
                        alert('Questo utente è già stato aggiunto!');
                        return;
                    }

                    const container = document.getElementById('members-container');
                    const memberRow = document.createElement('div');
                    memberRow.className = 'd-flex justify-content-between align-items-center border p-2 mb-2 bg-white rounded member-row';
                    memberRow.innerHTML = `
                        <span>${userName}</span>
                        <input type="hidden" name="users[]" value="${userId}">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.member-row').remove()">Rimuovi</button>
                    `;

                    container.appendChild(memberRow);
                    select.value = '';
                }
            </script>

            <div class="mb-4 mt-4">
                <label for="tags" class="form-label fw-bold">Tags</label>
                <input type="text" placeholder="Inserisci i tag separati da virgola (es. 'Biologia, Chimica')" class="form-control" id="tags" name="tags" value="{{ old('tags') }}">
            </div>

            <button type="submit" class="btn btn-primary btn-lg mb-5 w-100 fw-bold shadow-sm">Crea Progetto</button>
        </form>
    </div>
@endsection
