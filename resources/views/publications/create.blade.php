@extends('layouts.app')

@section('content')
<!-- Creazione di una nuova pubblicazione con tutti i campi presi dal database -->
<div class="container">
    <h1>Crea una nuova pubblicazione</h1>
    <form action="{{ route('publications.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Titolo</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <!--tipo di pubblicazione senza form-->
        <div class="mb-3">
            <label for="type" class="form-label">Tipo di pubblicazione</label>
            <input type="text" class="form-control" id="type" name="type" required>
        </div>
        <!--luogo della pubblicazione (venue) -->
        <div class="mb-3">
            <label for="venue" class="form-label">Luogo di pubblicazione</label>
            <input type="text" class="form-control" id="venue" name="venue" required>
        </div>
        <!-- DOI -->
        <div class="mb-3">
            <label for="doi" class="form-label">DOI</label>
            <input type="text" class="form-control" id="doi" name="doi" required>
        </div>
        <!-- stato della pubblicazione -->
        <div class="mb-3">
            <label for="status" class="form-label">Stato</label>
            <select class="form-select" id="status" name="status" required>
                <option value="">Seleziona uno stato</option>
                <option value="drafting">Bozza</option>
                <option value="submitted">Sottomesso</option>
                <option value="accepted">Accettato</option>
                <option value="published">Pubblicato</option>
            </select>
        </div>
        <!-- deadline della pubblicazione -->
        <div class="mb-3">
            <label for="target_deadline" class="form-label">Deadline della pubblicazione</label>
            <input type="date" class="form-control" id="target_deadline" name="target_deadline" required>
        </div>
        <!-- progetto associato alla pubblicazione -->
        <div class="mb-3">
            <label for="project_id" class="form-label">Progetto associato</label>
            <h2 class="text-muted small">Seleziona uno o più progetti associati a questa pubblicazione: </h2>
            <select class="form-select" id="projects" name="projects[]" required multiple>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                @endforeach
            </select>
        </div>
        <!-- autori associati alla pubblicazione -->
        <div class="author-row d-flex gap-2 mb-2">
            <select name="authors[user_id][]" class="form-select">
                <option value="">Seleziona autore</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <input type="number" name="authors[order][]" class="form-control" placeholder="Ordine" min="1" value="1" style="width:100px">
            <label class="d-flex align-items-center gap-1">
                <input type="checkbox" name="authors[is_corresponding][0]" value="1"> Corr.
            </label>
            <button type="button" class="btn btn-danger btn-sm remove-author">✕</button>
        </div>
        <button type="button" id="add-author" class="btn btn-secondary btn-sm mb-3">+ Aggiungi autore</button>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Crea pubblicazione</button>
        </div>
    </form>
</div>

<script>
document.getElementById('add-author').addEventListener('click', function() {
    var container = this.parentElement;
    var rows = container.querySelectorAll('.author-row');
    var newIndex = rows.length;

    var row = document.createElement('div');
    row.className = 'author-row d-flex gap-2 mb-2';

    // Clona il select dal primo row
    var firstSelect = rows[0].querySelector('select');
    var selectClone = firstSelect.cloneNode(true);
    selectClone.value = '';

    row.innerHTML = '';
    row.appendChild(selectClone);

    var orderInput = document.createElement('input');
    orderInput.type = 'number';
    orderInput.name = 'authors[order][]';
    orderInput.className = 'form-control';
    orderInput.placeholder = 'Ordine';
    orderInput.min = '1';
    orderInput.value = newIndex + 1;
    orderInput.style.width = '100px';
    row.appendChild(orderInput);

    var label = document.createElement('label');
    label.className = 'd-flex align-items-center gap-1';
    var checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.name = 'authors[is_corresponding][' + newIndex + ']';
    checkbox.value = '1';
    label.appendChild(checkbox);
    label.appendChild(document.createTextNode(' Corr.'));
    row.appendChild(label);

    var removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn btn-danger btn-sm remove-author';
    removeBtn.textContent = '✕';
    removeBtn.addEventListener('click', function() { row.remove(); });
    row.appendChild(removeBtn);

    container.insertBefore(row, this);
});

// Gestione rimozione per il primo row esistente
document.querySelectorAll('.remove-author').forEach(function(btn) {
    btn.addEventListener('click', function() {
        if (document.querySelectorAll('.author-row').length > 1) {
            this.closest('.author-row').remove();
        }
    });
});
</script>
@endsection
