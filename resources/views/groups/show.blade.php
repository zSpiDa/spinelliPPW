@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Gruppo di Ricerca</h1>
        <!-- Contenuto del gruppo di ricerca -->
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h5">{{ $group->name }}</h2>
                <div class="text-muted small">ID: {{ $group->id }}</div>
                <div class="mt-2">{{ $group->description }}</div>
            </div>
        </div>
        <!--Form per la modifica del gruppo -->
        <h3>Modifica Gruppo</h3>
        <form action="{{ route('groups.update') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $group->name) }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descrizione</label>
                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $group->description) }}</textarea>
            </div>
            <!--Form per aggiungere o rimuovere membri al gruppo -->
            <div class="mb-3">
                <h3 class="h6">Gestione Membri</h3>
                <div>
                    @forelse($group->users as $u)
                        <div class="border-bottom py-2 d-flex justify-content-between align-items-center">
                            <div class="small"><strong>{{ $u->name }}</strong> ({{ $u->email }})</div>
                            <form method="POST" action="{{ route('groups.removeMember', $u->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Rimuovere questo membro?')">
                                    Rimuovi
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-muted">Nessun membro assegnato.</div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer">
                <h3 class="h6">Aggiungi membro</h3>
                <form method="POST" action="{{ route('groups.addMember') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Seleziona utente</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">-- Seleziona utente --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Aggiungi membro</button>
                </form>
            </div>
        </form>    
        <!--Lista membri del gruppo -->
        <div class="card mt-4">
            <div class="card-body">
                <h3 class="h6">Membri del gruppo</h3>
                <div>
                    @forelse($group->users as $u)
                        <div class="border-bottom py-2 d-flex justify-content-between align-items-center">
                            <div class="small"><strong>{{ $u->name }}</strong> ({{ $u->email }})</div>
                        </div>
                    @empty
                        <div class="small">Nessun membro nel gruppo.</div>
                    @endforelse
                </div>
            </div>
        </div>
            <div class="mb-4">
                <!-- bottone salva modifiche -->
                <button type="submit" class="btn btn-primary">Salva modifiche</button>
            </div>
        
    </div>
@endsection