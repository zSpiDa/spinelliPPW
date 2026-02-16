@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crea Nuova Task</h1>
    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Titolo</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Descrizione</label>
            <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="due_date" class="form-label">Data di scadenza</label>
            <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}">
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Stato</label>
            <select class="form-select" id="status" name="status" required>
                <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>Aperto</option>
                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In corso</option>
                <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Completato</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="priority" class="form-label">Priorità</label>
            <select class="form-select" id="priority" name="priority" required>
                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Bassa</option>
                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Media</option>
                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="assignee_id" class="form-label">Assegnato a</label>
            <select class="form-select" id="assignee_id" name="assignee_id">
                <option value="">-- Nessuno --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('assignee_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="project_id" class="form-label">Progetto</label>
            <select class="form-select" id="project_id" name="project_id">
                <option value="">-- Nessuno --</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Crea Task</button>
    </form>
</div>
@endsection
