@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">

                    <div class="card-header fw-bold">Modifica Progetto: {{ $project->title }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('projects.update', $project) }}">
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
                                    <option value="active" {{ $project->status == 'active' ? 'selected' : '' }}>Planned</option>
                                    <option value="ongoing" {{ $project->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="draft" {{ $project->status == 'draft' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="code" class="form-label">Codice</label>
                                <input type="text"
                                       class="form-control"
                                       id="code"
                                       name="code"
                                       value="{{ old('code', $project->code) }}">
                            </div>
                            <div class="mb-3">
                                <label for="funder" class="form-label">Funder</label>
                                <input type="text"
                                       class="form-control"
                                       id="funder"
                                       name="funder"
                                       value="{{ old('funder', $project->funder) }}">
                            </div>
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Data inizio</label>
                                <input type="date"
                                       class="form-control"
                                       id="start_date"
                                       name="start_date"
                                       value="{{ old('start_date', $project->start_date) }}">
                            </div>
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Data fine</label>
                                <input type="date"
                                       class="form-control"
                                       id="end_date"
                                       name="end_date"
                                       value="{{ old('end_date', $project->end_date) }}">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Descrizione</label>
                                <textarea class="form-control"
                                          id="description"
                                          name="description"
                                          rows="4">{{ old('description', $project->description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="milestones" class="form-label">Milestones (formato: titolo|data_scadenza|stato, separate da virgola)</label>
                                <input type="text"
                                       class="form-control"
                                       id="milestones"
                                       name="milestones"
                                       value="{{ old('milestones') }}">
                            </div>
                            <div class="mb-3">
                                <label for="publications" class="form-label">Publications (formato: titolo|anno, separate da virgola)</label>
                                <input type="text"
                                       class="form-control"
                                       id="publications"
                                       name="publications"
                                       value="{{ old('publications') }}">
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

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('projects.index') }}" class="btn btn-secondary">Annulla</a>
                                <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
