@extends('layouts.app')
@section('content')
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Ciao, {{ $user->name }}</h1>
            <h4 class="text-muted">Ecco una panoramica delle tue attività recenti.</h4>
        </div>
    </div>
    {{-- Cards riepilogo --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <div class="fs-2 fw-bold text-primary">{{ $projects->count() }}</div>
                    <div class="text-muted small">Progetti</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <div class="fs-2 fw-bold text-success">{{ $tasks->count() }}</div>
                    <div class="text-muted small">Task totali</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <div class="fs-2 fw-bold text-warning">{{ $tasks->where('status', 'in_progress')->count() }}</div>
                    <div class="text-muted small">Task in corso</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <div class="fs-2 fw-bold text-info">{{ $publications->unique('id')->count() }}</div>
                    <div class="text-muted small">Pubblicazioni</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Progetti associati --}}
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Progetti associati</div>
                <div class="card-body p-0">
                    @forelse($projects as $project)
                        <a href="{{ route('projects.show', $project->id) }}" class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom text-decoration-none text-dark">
                            <div>
                                <div class="fw-semibold">{{ $project->title }}</div>
                                <small class="text-muted">{{ $project->code ?? '' }}</small>
                            </div>
                            <span class="badge text-bg-{{ $project->status === 'active' ? 'success' : ($project->status === 'completed' ? 'secondary' : 'warning') }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </a>
                    @empty
                        <div class="p-3 text-muted">Non sei associato a nessun progetto.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Task --}}
        <div class="col-12 col-md-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">I tuoi Task</div>
                <div class="card-body p-0">
                    @forelse($tasks->sortBy('due_date')->take(10) as $task)
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">{{ $task->title }}</div>
                                <small class="text-muted">{{ $task->project?->title ?? 'N/A' }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge text-bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                                @if($task->due_date)
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-3 text-muted">Non hai task attivi.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
