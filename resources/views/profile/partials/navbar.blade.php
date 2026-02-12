<ul class="navbar-nav ms-auto">
    @auth
        {{-- Info Utente --}}
        <li class="nav-item"><span class="nav-link">Ciao, {{ Auth::user()->name }} ({{ Auth::user()->role }})</span></li>

        {{-- Link Progetti --}}
        <li class="nav-item"><a class="nav-link" href="{{ route('projects.index') }}">Progetti</a></li>

        {{-- Link Pubblicazioni --}}
        <li class="nav-item"><a class="nav-link" href="{{ route('publications.index') }}">Pubblicazioni</a></li>

        {{-- NUOVO LINK TASKS --}}
        {{-- Nota: questo funziona solo se hai aggiunto Route::resource('tasks'...) in web.php come detto prima --}}
        <li class="nav-item"><a class="nav-link" href="{{ route('tasks.index') }}">Tasks</a></li>

        {{-- Logout --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout
            </a>
        </li>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>
    @endauth
</ul>
