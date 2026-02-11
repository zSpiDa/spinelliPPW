@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12"> {{-- Uso col-12 per avere più spazio per la tabella --}}
                <div class="card">

                    {{-- Intestazione stile Card, come nel tuo esempio --}}
                    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                        <span>Tutte le Tasks</span>
                    </div>

                    <div class="card-body">

                        {{-- Tabella Bootstrap --}}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th>Task</th>
                                    <th>Stato</th>
                                    <th>Collegato a</th>
                                    <th>Assegnato a</th>
                                    <th class="text-end">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($tasks as $task)
                                    <tr>
                                        {{-- Colonna Titolo e Scadenza --}}
                                        <td>
                                            <div class="fw-bold">{{ $task->title }}</div>
                                            @if($task->due_date)
                                                <small class="text-muted">
                                                    Scadenza: {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                                </small>
                                            @endif
                                        </td>

                                        {{-- Colonna Stato (Badge Bootstrap) --}}
                                        <td>
                                            @php
                                                $badgeClass = match($task->status) {
                                                    'completed' => 'bg-success',
                                                    'in_progress' => 'bg-primary',
                                                    'todo' => 'bg-secondary',
                                                    default => 'bg-light text-dark border'
                                                };
                                            @endphp
                                            <span class="badge rounded-pill {{ $badgeClass }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                        </td>

                                        {{-- Colonna Collegamenti (Progetto/Milestone) --}}
                                        <td>
                                            <div>
                                                <small class="text-muted">Progetto:</small><br>
                                                <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none fw-bold">
                                                    {{ $task->project->title }}
                                                </a>
                                            </div>
                                            @if($task->milestone)
                                                <div class="mt-1">
                                                    <small class="text-muted">Milestone:</small>
                                                    <span>{{ $task->milestone->title }}</span>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Colonna Assegnatario --}}
                                        <td>
                                            @if($task->user)
                                                <span class="badge bg-info text-dark">{{ $task->user->name }}</span>
                                            @else
                                                <span class="text-muted fst-italic">Non assegnato</span>
                                            @endif
                                        </td>

                                        {{-- Colonna Azioni --}}
                                        <td class="text-end">
                                            <a href="{{ route('projects.show', $task->project) }}" class="btn btn-sm btn-outline-secondary">
                                                Vedi nel Progetto
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Nessuna attività trovata.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginazione (se ci sono molti task) --}}
                        <div class="mt-3">
                            {{ $tasks->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
