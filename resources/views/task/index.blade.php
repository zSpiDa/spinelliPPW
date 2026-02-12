@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                        <span>Tutte le Tasks</span>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th>Task</th>
                                    <th>Stato</th>
                                    <th>Priorità</th>
                                    <th>Collegato a</th>
                                    <th>Assegnato a</th>
                                    <th>Modifica</th>
                                    <th class="text-end">Elimina</th> </tr>
                                </thead>
                                <tbody>
                                @forelse($tasks as $task)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ str_replace('Task: ', '', $task->title) }}</div>
                                            @if($task->due_date)
                                                <small class="text-muted">
                                                    Scadenza: {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                                </small>
                                            @endif
                                        </td>

                                        <td>
                                            @php
                                                $statusBtnClass = match($task->status) {
                                                    'done', 'completed' => 'btn-outline-success', // Verde
                                                    'in_progress', 'ongoing' => 'btn-outline-warning', // Giallo
                                                    'open', 'todo' => 'btn-outline-danger',  // Rosso
                                                    default => 'btn-outline-secondary'
                                                };

                                                $statusLabel = match($task->status) {
                                                    'done', 'completed' => 'Completato',
                                                    'in_progress', 'ongoing' => 'In Corso',
                                                    'open', 'todo' => 'Da Fare',
                                                    default => ucfirst($task->status)
                                                };
                                            @endphp
                                            <span class="btn btn-sm {{ $statusBtnClass }} fw-bold disabled py-0 px-2"
                                                  style="opacity: 1; font-size: 0.75rem;">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>

                                        <td>
                                            @php
                                                $prioBtnClass = match($task->priority) {
                                                    'high' => 'btn-outline-danger',   // Rosso
                                                    'medium' => 'btn-outline-warning',// Giallo
                                                    'low' => 'btn-outline-success',   // Verde
                                                    default => 'btn-outline-secondary'
                                                };

                                                $prioLabel = match($task->priority) {
                                                    'high' => 'Alta',
                                                    'medium' => 'Media',
                                                    'low' => 'Bassa',
                                                    default => 'N/D'
                                                };
                                            @endphp
                                            <span class="btn btn-sm {{ $prioBtnClass }} fw-bold disabled py-0 px-2"
                                                  style="opacity: 1; font-size: 0.75rem;">
                                                {{ $prioLabel }}
                                            </span>
                                        </td>

                                        <td>
                                            <div>
                                                @if($task->project)
                                                    <small class="text-muted">Progetto:</small><br>
                                                    <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none fw-bold">
                                                        {{ $task->project->title }}
                                                    </a>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            @if($task->user)
                                                <span class="badge bg-info text-dark">{{ $task->user->name }}</span>
                                            @else
                                                <span class="text-muted small fst-italic">-- Nessuno --</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning">
                                                Modifica
                                            </a>
                                        </td>

                                        <td class="text-end">
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler eliminare questa task?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    Elimina
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Nessuna attività trovata.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-2">
                            {{ $tasks->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
