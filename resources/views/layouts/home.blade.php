<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 col-md-8">
            <h2 class="h5">Benvenuto nel Gestionale del Gruppo di Ricerca</h2>
            <p>Questa home mostra dati sintetici; i dettagli sono nelle sezioni dedicate.</p>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <span class="fw-semibold">Progetti totali</span>
                    <span class="badge text-bg-primary">{{ $projectCount }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
