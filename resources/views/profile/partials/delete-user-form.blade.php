<!-- Cancellazione account -->
<section class="mt-5">
    <header>
        <h2 class="h4 text-danger">Elimina Account</h2>
        <p>Una volta eliminato il tuo account, tutte le sue risorse e dati saranno eliminati definitivamente. Prima di eliminare il tuo account, scarica qualsiasi dato o informazione che desideri conservare.</p>
    </header>
    <form method="POST" action="{{ route('profile.destroy') }}" class="mt-4">
        @csrf
        @method('DELETE')
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Inserisci la tua password per confermare" required>
            @error('password')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-danger">Elimina Account</button>
    </form>
</section>