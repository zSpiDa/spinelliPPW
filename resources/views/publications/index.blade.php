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

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                @forelse($publications as $pub)
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center p-4 border-bottom">

                        {{-- Sinistra: Titolo e Info base --}}
                        <div class="mb-3 mb-md-0 pe-md-4">
                            <div class="fw-bold text-dark fs-5 mb-1">{{ $pub->title }}</div>
                            <div class="text-muted small">
                                {{ ucfirst($pub->type ?? 'N/D') }} &bull; {{ $pub->venue ?? 'N/D' }}
                            </div>
                        </div>

                        {{-- Destra: Azioni (Stile Task) --}}
                        <div class="d-flex gap-2 flex-shrink-0">
                            <a href="{{ route('publications.show', $pub->id) }}" class="btn btn-sm btn-outline-primary fw-bold px-3">
                                Dettagli
                            </a>
                            <a href="{{ route('publications.edit', $pub->id) }}" class="btn btn-sm btn-outline-warning fw-bold px-3">
                                Modifica
                            </a>
                            <form action="{{ route('publications.destroy', $pub->id) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Vuoi davvero eliminare questa pubblicazione?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold px-3">
                                    Elimina
                                </button>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="p-5 text-center text-muted">
                        Nessuna pubblicazione presente.
                    </div>
                @endforelse
            </div>

            @if(method_exists($publications, 'links'))
                <div class="card-footer bg-white mt-2 border-0">
                    {{ $publications->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
