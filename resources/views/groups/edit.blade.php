@extends('layouts.app')

@section('content')
    <a href="{{ route('dashboard') }}" class="btn btn-link p-0 mb-3">← Torna alla dashboard</a>
    <div class="container">
        <h1>Modifica Gruppo di Ricerca</h1>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form action="{{ route('groups.update') }}" method="POST" class="mb-4">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nome del Gruppo</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $group->name }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descrizione</label>
                <textarea name="description" id="description" class="form-control">{{ $group->description }}</textarea>
            </div>
            <div class="card">
            <div class="card-body">
                <h2 class="h5">Membri del Gruppo ({{ $memberCount }})</h2>
                <ul class="list-group mb-3">
                    @foreach($group->users as $user)
                        <li class="list-group-item">{{ $user->name }} ({{ $user->email }})</li>
                    @endforeach
                </ul>
            </div>
        </div>
        </form>
        <!-- Form per aggiungere membri al gruppo -->
        <form action="{{ route('groups.addMember') }}" method="POST" class="mb-4">
            @csrf
            <div class="mb-3">
                <label for="user_id" class="form-label">Aggiungi Membro</label>
                <select name="user_id" id="user_id" class="form-select">
                    @foreach($users as $u)
                        @if(!$group->users->contains($u))
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Aggiungi Membro</button>
        </form>
        <form action="{{ route('groups.update') }}" method="POST" class="mb-4">
            @csrf
            <button type="submit" class="btn btn-primary">Salva Modifiche</button>
        </form>
    </div>
@endsection