@extends('layouts.app')

@section('content')
    <div class="container py-4">

        @if(session('success'))
            <div class="alert alert-success mb-4 rounded">{{ session('success') }}</div>
        @endif

        <div class="mb-4 text-start">
            <h3 class="fw-bold mb-3">Tutte le Pubblicazioni</h3>

            @if(auth()->user() && in_array(auth()->user()->role, ['pi', 'manager', 'researcher']))
                <a href="{{ route('publications.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    + Nuova pubblicazione
                </a>
            @endif
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="text-dark fw-bold">Titolo</th>
                        <th class="text-dark fw-bold">Stato</th>
                        <th class="text-dark fw-bold">Progetti</th>
                        <th class="text-dark fw-bold">Autori</th>
                        <th class="text-dark fw-bold text-center">Dettagli</th>
                        <th class="text-dark fw-bold text-center">Modifica</th>
                        <th class="text-dark fw-bold text-center">Elimina</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($publications as $pub)
                        <tr>
                            <td>
                                <a href="{{ route('publications.show', $pub->id) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $pub->title }}
                                </a>
                                <div class="text-muted small mt-1">
                                    {{ $pub->type ?? 'N/D' }} &bull; {{ $pub->venue ?? 'N/D' }}
                                </div>
                            </td>

                            <td>
                                @php
                                    $statusStyle = match($pub->status) {
                                        'published' => ['class' => 'border-success text-success', 'label' => 'Pubblicata'],
                                        'accepted'  => ['class' => 'border-warning text-dark', 'label' => 'Accettata'],
                                        'submitted' => ['class' => 'border-primary text-primary', 'label' => 'Inviata'],
                                        'drafting'  => ['class' => 'border-danger text-danger', 'label' => 'In Bozza'],
                                        default     => ['class' => 'border-secondary text-secondary', 'label' => ucfirst($pub->status)],
                                    };
                                @endphp
                                <span class="badge border bg-white {{ $statusStyle['class'] }} rounded-pill px-3 py-2" style="font-weight: 500; font-size: 0.8rem;">
                                    {{ $statusStyle['label'] }}
                                </span>
                            </td>

                            <td>
                                @if($pub->projects->count() > 0)
                                    @foreach($pub->projects as $project)
                                        <div class="small">
                                            <span class="text-muted">Progetto:</span><br>
                                            <a href="{{ route('projects.show', $project->id) }}" class="text-primary text-decoration-none fw-bold">
                                                {{ $project->title }}
                                            </a>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-muted small">Nessun progetto</span>
                                @endif
                            </td>

                            <td>
                                <div class="small fw-semibold text-dark">
                                    @foreach($pub->authors->sortBy('order') as $a)
                                        <span class="mr-1">
                                            {{ $a->name ?? $a->user->name ?? 'N/D' }}@if(optional($a->pivot)->is_corresponding)<sup class="text-danger fw-bold">*</sup>@endif
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('publications.show', $pub->id) }}" class="btn btn-outline-primary btn-sm">
                                    Dettagli
                                </a>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('publications.edit', $pub->id) }}" class="btn btn-outline-warning btn-sm">
                                    Modifica
                                </a>
                            </td>

                            <td class="text-center">
                                <form action="{{ route('publications.destroy', $pub->id) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Vuoi davvero eliminare questa pubblicazione?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        Elimina
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Nessuna pubblicazione presente.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($publications, 'links'))
                <div class="card-footer bg-white mt-2 border-0">
                    {{ $publications->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
