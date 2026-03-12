@extends('layouts.app')

<!--form creazione progetto con campi per titolo, stato, codice, funder, date e descrizione -->
@section('content')
    <h1>Crea nuovo progetto</h1>

    <form action="{{ route('projects.store') }}" enctype="multipart/form-data" method="POST">
        @csrf

        @if ($errors->any())
        <div class="alert alert-danger">
        <ul>
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
            <!-- select con freccettina per selezionare lo stato del progetto -->
            <h5 for="status" class="form-label">Stato</h5>
            <select class="form-select" id="status" name="status">
                <option value="planned" {{ old('status') == 'active' ? 'selected' : '' }}>Pianificato</option>
                <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>In corso</option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected ' : '' }}>Completato</option>
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
        <div class="mb-3">
            <h5>Milestone</h5>
            <div id="milestones-container">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addMilestone()">
                + Aggiungi milestone
            </button>
        </div>
                <script>
                    let milestoneIndex = 0;
                    function addMilestone() {
                        const container = document.getElementById('milestones-container');
                        const row = document.createElement('div');
                        row.className = 'row g-2 mb-2';
                        row.innerHTML = `
                            <div class="mt-4 col">
                                <input type="text" class="form-control" name="milestones[${milestoneIndex}][title]" placeholder="Titolo">
                            </div>
                            <div class="mt-4 col">
                                <input type="date" class="form-control" name="milestones[${milestoneIndex}][due_date]" placeholder="Data scadenza">
                            </div>
                            <div class="mt-4 col">
                                <select class="form-select" name="milestones[${milestoneIndex}][status]">
                                    <option value="planned">Pianificato</option>
                                    <option value="ongoing">In corso</option>
                                    <option value="completed">Completato</option>
                                </select>
                            </div>
                            <div class="mt-4 col-auto">
                                <button type="button" class="btn btn-danger" onclick="this.closest('.row').remove()">X</button>
                            </div>
                        `;
                        container.appendChild(row);
                        milestoneIndex++;
                    }
                </script>
                <br>
        <div class="mb-3">
            <h5 for="tags" class="form-label">Tags</h5>
            <input type="text" placeholder="Inserisci i tag separati da virgola (es. 'Biologia, Chimica')" class="form-control" id="tags" name="tags" value="{{ old('tags') }}">
        </div>
        <div class="mb-3">
            <h5 for="file" class="form-label">Allega file</h5>
            <input type="file" class="form-control" id="file" name="file" accept=".pdf">
        </div>
        <button type="submit" class="btn btn-primary">Crea progetto</button>
    </form>
@endsection
