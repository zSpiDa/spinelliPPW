@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Modifica Milestone: {{ $milestone->title }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('milestones.update', $milestone->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="title" class="form-label">Titolo</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $milestone->title) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="due_date" class="form-label">Scadenza</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $milestone->due_date) }}">
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Stato</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="planned" {{ $milestone->status == 'planned' ? 'selected' : '' }}>Planned</option>
                                    <option value="ongoing" {{ $milestone->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="completed" {{ $milestone->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('projects.show', $milestone->project_id) }}" class="btn btn-secondary">Annulla</a>
                                <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
