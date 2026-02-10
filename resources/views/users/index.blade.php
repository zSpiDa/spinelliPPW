<!-- index per la lista degli utenti -->
@extends('layouts.app')

@section('content')
    <h2 class="h5 mb-3">Utenti</h2>

    @forelse ($users as $u)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="h6 m-0">{{ $u->name }}</h3>
                        <div class="text-muted small">{{ $u->email }}</div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-muted">Nessun utente trovato.</div>
    @endforelse
@endsection
