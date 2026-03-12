@php
    // Determiniamo se siamo in modalità modifica controllando se la pubblicazione ha un ID
    $edit = isset($publication) && $publication->exists;
@endphp

{{-- Visualizzazione errori generali --}}
@if ($errors->any())
    <div class="alert alert-danger mb-4 p-3 rounded bg-red-50 text-red-800">
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- 1. TITOLO --}}
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700"><b>Titolo: </b></label>
        <input type="text" name="title"
               value="{{ old('title', $publication->title ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" required>
    </div>

    {{-- 2. TIPO --}}
    <div>
        <label class="block text-sm font-medium text-gray-700"><b>Tipo: </b></label>
        <input type="text" name="type"
               value="{{ old('type', $publication->type ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2">
    </div>

    {{-- 3. VENUE --}}
    <div>
        <label class="block text-sm font-medium text-gray-700"><b>Luogo: </b></label>
        <input type="text" name="venue"
               value="{{ old('venue', $publication->venue ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2">
    </div>

    {{-- 4. STATO --}}
    <div>
        <label class="block text-sm font-medium text-gray-700"><b>Stato:</b></label>
        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2">
            @foreach($statuses as $status)
                <option value="{{ $status }}"
                    @selected(old('status', $publication->status ?? '') == $status)>
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- 5. DEADLINE --}}
    <div>
        <label class="block text-sm font-medium text-gray-700"><b>Target Deadline: </b></label>
        <input type="date" name="target_deadline"
               value="{{ old('target_deadline', isset($publication->target_deadline) ? $publication->target_deadline->format('Y-m-d') : '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2">
    </div>

    {{-- 6. DOI --}}
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700"><b>DOI (Link o codice): </b></label>
        <input type="text" name="doi"
               value="{{ old('doi', $publication->doi ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2">
    </div>

    {{-- 7. PROGETTI COLLEGATI (Multi-select) --}}
    <div class="col-span-2">
        <label class="mt-1 block text-sm font-medium text-gray-700 mb-1"><b>Progetti Collegati</b> (Puoi selezionare più progetti cercandoli per nome):</label>

        <select id="select-projects" name="projects[]" multiple autocomplete="off"
                class="block w-full rounded-md border-gray-300 shadow-sm border p-2"
                placeholder="Cerca o seleziona un progetto...">
            @foreach($projects as $p)
                @php
                    $oldProjects = old('projects', []);
                    $dbProjects = $edit ? $publication->projects->pluck('id')->toArray() : [];
                    $isSelected = $oldProjects ? in_array($p->id, $oldProjects) : in_array($p->id, $dbProjects);
                @endphp
                <option value="{{ $p->id }}" @selected($isSelected)>
                    {{ $p->title }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- 8. GESTIONE AUTORI (La parte complessa che abbiamo sistemato) --}}
    <div class="col-span-2 border-t pt-4 mt-4">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Autori: </h3>

        <div id="authors-list">
            @php
                // 1. Dati esistenti (se edit)
                $existing = $edit ? $publication->authors->sortBy('order')->values() : collect();
                // 2. Dati old input (se errore validazione)
                $oldAuthors = old('authors');
                // 3. Calcolo righe
                $rowsCount = max(1, $oldAuthors ? count($oldAuthors) : $existing->count());
            @endphp

            {{-- Attributo data-next-index fondamentale per il JS --}}
            <div id="authors-container" data-next-index="{{ $rowsCount }}">
                @for($i=0; $i < $rowsCount; $i++)
                    <div class="flex items-center gap-2 mb-2 author-row p-2 bg-gray-50 rounded border">
                        {{-- Select Utente --}}
                        <div class="flex-grow">
                            <label class="text-xs text-gray-500">Utente</label>
                            <select name="authors[{{ $i }}][user_id]" class="block w-full border-gray-300 rounded-md shadow-sm border p-1">
                                <option value="">-- Seleziona --</option>
                                @foreach($users as $u)
                                    @php
                                        $oldVal = old("authors.$i.user_id");
                                        $dbVal = optional($existing->get($i))->user_id;
                                        $sel = $oldAuthors ? ($oldVal == $u->id) : ($dbVal == $u->id);
                                    @endphp
                                    <option value="{{ $u->id }}" @selected($sel)>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
<br>
                        {{-- Ordine --}}
                        <div class="w-20">
                            <label class="text-xs text-gray-500">Ordine: </label>
                            <input type="number" name="authors[{{ $i }}][order]"
                                   value="{{ old("authors.$i.order", optional($existing->get($i))->order ?? ($i+1)) }}"
                                   class="block w-full border-gray-300 rounded-md shadow-sm border p-1">
                        </div>

                        {{-- Checkbox Corresponding --}}
                        <div class="flex items-center mt-4 px-2">
                            <input type="hidden" name="authors[{{ $i }}][is_corresponding]" value="0">
                            @php
                                $isCorr = old("authors.$i.is_corresponding", optional($existing->get($i))->is_corresponding);
                            @endphp
                            <input type="checkbox" name="authors[{{ $i }}][is_corresponding]" value="1" @checked($isCorr)
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Autore della pubblicazione</span>
                        </div>

                        {{-- Tasto Rimuovi --}}
                        <div class="mt-4">
                            <button type="button" class="text-red-600 hover:text-red-800" onclick="this.closest('.author-row').remove()">
                                &times;
                            </button>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <button type="button" onclick="addAuthorRow()" class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
            + Aggiungi Autore
        </button>
    </div>

    {{-- 9. FILE UPLOAD --}}
    <div class="col-span-2 border-t pt-4 mt-4">
        <label class="block text-sm font-medium text-gray-700"><b>PDF Principale: </b></label>
        <input type="file" name="main_pdf" accept=".pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        @if($edit && $publication->attachments()->where('type', 'main_pdf')->exists())
            <p class="text-xs text-green-600 mt-1">✓ File già caricato. Caricane uno nuovo per sostituirlo.</p>
        @endif
    </div>

    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700"><b>Materiali Aggiuntivi: </b></label>
        <input type="file" name="materials[]" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
    </div>

</div>

{{-- JAVASCRIPT PER GLI AUTORI --}}
<script>
    function addAuthorRow() {
        let container = document.getElementById('authors-container');
        // Prendo l'indice dal data-attribute per evitare conflitti
        let index = parseInt(container.getAttribute('data-next-index'));

        // Aggiorno l'attributo per il prossimo click
        container.setAttribute('data-next-index', index + 1);

        // Template HTML usando backticks (`)
        let template = `
        <div class="flex items-center gap-2 mb-2 author-row p-2 bg-gray-50 rounded border">
            <div class="flex-grow">
                <select name="authors[${index}][user_id]" class="block w-full border-gray-300 rounded-md shadow-sm border p-1">
                    <option value="">-- Seleziona --</option>
                    @foreach($users as $u)
        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
        </select>
    </div>
    <div class="w-20">
        <input type="number" name="authors[${index}][order]" value="${index + 1}" class="block w-full border-gray-300 rounded-md shadow-sm border p-1">
            </div>
            <div class="flex items-center mt-0 px-2">
                <input type="hidden" name="authors[${index}][is_corresponding]" value="0">
                <input type="checkbox" name="authors[${index}][is_corresponding]" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <span class="ml-2 text-sm text-gray-700">Autore della pubblicazione</span>
            </div>
            <div>
                <button type="button" class="text-red-600 hover:text-red-800" onclick="this.closest('.author-row').remove()">
                    &times;
                </button>
            </div>
        </div>
    `;

        container.insertAdjacentHTML('beforeend', template);
    }
</script>

{{-- CSS e JS per il menu a tendina (Tom Select) --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<style>
    /* Piccola correzione per rendere il design identico al resto del form */
    .ts-control {
        border-radius: 0.375rem; /* rounded-md */
        padding: 0.5rem; /* p-2 */
        border-color: #d1d5db; /* grigio standard */
    }
    /* Colore dei tag selezionati */
    .ts-wrapper.multi .ts-control > div {
        background: #eff6ff; /* blu chiarissimo */
        color: #1e40af; /* blu scuro */
        border: 1px solid #dbeafe;
        border-radius: 4px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function(){
        new TomSelect("#select-projects", {
            plugins: ['remove_button'], // Aggiunge la "x" per rimuovere la selezione
            create: false,              // Impedisce di creare progetti scrivendo testo a caso
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "Cerca un progetto...",
            maxOptions: null // Mostra tutti i risultati
        });
    });
</script>
