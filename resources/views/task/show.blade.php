@extends('layouts.app')

@section('content')
    <div class="container">
        <a href="{{ route('tasks.index') }}" class="btn btn-link p-0 mb-3 text-decoration-none">
            ← Torna a Tasks
        </a>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h2 class="h3 fw-bold mb-0">
                                {{ str_replace('Task: ', '', $task->title) }}
                            </h2>

                            <div class="d-flex gap-2">
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-warning fw-bold px-3">
                                    Modifica
                                </a>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questa task?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-bold px-3">
                                        Elimina
                                    </button>
                                </form>
                            </div>
                        </div>

                        <hr class="mb-4">

                        <h5 class="fw-bold mb-3">Descrizione</h5>
                        @if($task->description)
                            <div class="text-secondary" style="white-space: pre-line;">
                                {{ $task->description }}
                            </div>
                        @else
                            <div class="text-muted fst-italic">
                                Nessuna descrizione inserita per questa task.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-bold py-3">
                        Dettagli Task
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">

                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <span class="text-muted fw-bold small">Stato</span>
                                @php
                                    $statusBtnClass = match($task->status) {
                                        'done', 'completed' => 'bg-success',
                                        'in_progress', 'ongoing' => 'bg-warning text-dark',
                                        'open', 'todo' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    $statusLabel = match($task->status) {
                                        'done', 'completed' => 'Completato',
                                        'in_progress', 'ongoing' => 'In Corso',
                                        'open', 'todo' => 'Da Fare',
                                        default => ucfirst($task->status)
                                    };
                                @endphp
                                <span class="badge {{ $statusBtnClass }} px-3 py-2 rounded-pill">
                                    {{ $statusLabel }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <span class="text-muted fw-bold small">Priorità</span>
                                @php
                                    $prioBtnClass = match($task->priority) {
                                        'high' => 'bg-danger',
                                        'medium' => 'bg-warning text-dark',
                                        'low' => 'bg-success',
                                        default => 'bg-secondary'
                                    };
                                    $prioLabel = match($task->priority) {
                                        'high' => 'Alta',
                                        'medium' => 'Media',
                                        'low' => 'Bassa',
                                        default => 'N/D'
                                    };
                                @endphp
                                <span class="badge {{ $prioBtnClass }} px-3 py-2 rounded-pill">
                                    {{ $prioLabel }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <span class="text-muted fw-bold small">Scadenza</span>
                                <span class="fw-bold">
                                    @if($task->due_date)
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted fst-italic">Nessuna</span>
                                    @endif
                                </span>
                            </li>

                            <li class="list-group-item py-3">
                                <div class="text-muted fw-bold small mb-1">Assegnato a</div>
                                <div>
                                    @if($task->user)
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="fw-bold text-primary">{{ $task->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">-- Nessun assegnatario --</span>
                                    @endif
                                </div>
                            </li>

                            <li class="list-group-item py-3">
                                <div class="text-muted fw-bold small mb-1">Collegato al Progetto</div>
                                <div>
                                    @if($task->project)
                                        <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none fw-bold">
                                            {{ $task->project->title }}
                                        </a>
                                    @else
                                        <span class="text-muted fst-italic">-- Nessun progetto --</span>
                                    @endif
                                </div>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
