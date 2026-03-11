@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Crea nuovo progetto</h1>

        <form action="{{ route('projects.store') }}" enctype="multipart/form-data" method="POST">
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
                <h5 for="title" class="form-label">Titolo</h5>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}">
            </div>

            <div class="mb-3">
                <h5 for="status" class="form-label">Stato</h5>
                <select class="form-select" id="status" name="status">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Pianificato</option>
                    <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>In corso</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completato</option>
                </select>
            </div>

            <div class="mb-3">
                <h5 for="code" class="form-label">Codice</h5>
                <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}">
            </div>

            <div class="mb-3">
                <h5 for="funder" class="form-label">Funder</h5>
                <input type="text" class="form-control" id="funder" name="funder" value="{{ old('funder') }}">
            </div>

            <div class="mb-3">
                <h5 for="start_date" class="form-label">Data inizio</h5>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}">
            </div>

            <div class="mb-3">
                <h5 for="end_date" class="form-label">Data fine</h5>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}">
            </div>

            <div class="mb-3">
                <h5 for="description" class="form-label">Descrizione</h5>
                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4 p-3 bg-light rounded border">
                <h5 class="form-label">Membri del Progetto</h5>

                <div id="members-container" class="mb-3">
                    {{-- Ripristina vecchi valori se la validazione fallisce --}}
                    @if(old('users'))
                        @foreach($users as $user)
                            @if(in_array($user->id, old('users')))
                                <div class="d-flex justify-content-between align-items-center border p-2 mb-2 bg-white rounded">
                                    <span>{{ $user->name }} ({{ $user->role ?? 'Utente' }})</span>
                                    <input type="hidden" name="users[]" value="{{ $user->id }}">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('div').remove()">Rimuovi</button>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="input-group">
                    <select id="user-select" class="form-select">
                        <option value="">-- Seleziona utente da aggiungere --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role ?? 'Utente' }})</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-primary fw-bold" onclick="addMember()">Aggiungi Membro</button>
                </div>
            </div>

            <script>
                function addMember() {
                    const select = document.getElementById('user-select');
                    const userId = select.value;
                    const userName = select.options[select.selectedIndex].text;

                    // Se non è selezionato nulla, esci
                    if (!userId) return;

                    // Evita di aggiungere lo stesso utente due volte
                    if (document.querySelector(`input[name="users[]"][value="${userId}"]`)) {
                        alert('Questo utente è già stato aggiunto!');
                        return;
                    }

                    // Crea la riga HTML per il nuovo membro
                    const container = document.getElementById('members-container');
                    const memberRow = document.createElement('div');
                    memberRow.className = 'd-flex justify-content-between align-items-center border p-2 mb-2 bg-white rounded shadow-sm';
                    memberRow.innerHTML = `
                        <span class="fw-semibold">${userName}</span>
                        <input type="hidden" name="users[]" value="${userId}">
                        <button type="button" class="btn btn-outline-danger btn-sm fw-bold px-3" onclick="this.closest('div').remove()">Rimuovi</button>
                    `;

                    // Aggiungi la riga al contenitore
                    container.appendChild(memberRow);

                    // Resetta la tendina
                    select.value = '';
                }
            </script>
            <div class="mb-3 p-3 bg-light rounded border">
                <h5 class="mb-3">Milestone</h5>
                <div id="milestones-container">
                    {{-- Le milestone dinamiche appariranno qui --}}
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary fw-bold mt-2" onclick="addMilestone()">
                    + Aggiungi milestone
                </button>
            </div>

            <script>
                let milestoneIndex = 0;
                function addMilestone() {
                    const container = document.getElementById('milestones-container');
                    const row = document.createElement('div');
                    row.className = 'row g-2 mb-2 align-items-center bg-white p-2 border rounded shadow-sm';
                    row.innerHTML = `
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="milestones[${milestoneIndex}][title]" placeholder="Titolo Milestone">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" name="milestones[${milestoneIndex}][due_date]">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="milestones[${milestoneIndex}][status]">
                                <option value="active">Pianificato</option>
                                <option value="ongoing">In corso</option>
                                <option value="completed">Completato</option>
                            </select>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-outline-danger btn-sm fw-bold w-100" onclick="this.closest('.row').remove()">Rimuovi</button>
                        </div>
                    `;
                    container.appendChild(row);
                    milestoneIndex++;
                }
            </script>

            <div class="mb-3">
                <h5 for="tags" class="form-label">Tags</h5>
                <input type="text" placeholder="Inserisci i tag separati da virgola" class="form-control" id="tags" name="tags" value="{{ old('tags') }}">
            </div>

            <div class="mb-4">
                <h5 for="file" class="form-label">Allega file</h5>
                <input type="file" class="form-control" id="file" name="file" accept=".pdf">
            </div>

            <button type="submit" class="btn btn-success btn-lg w-100 mb-5 fw-bold shadow-sm">Crea progetto</button>
        </form>
    </div>
@endsection
