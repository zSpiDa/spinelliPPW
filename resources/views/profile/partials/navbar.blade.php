<ul class="navbar-nav ms-auto">
    @auth
        <li class="nav-item"><span class="nav-link">Ciao, {{ Auth::user()->name }} ({{ Auth::user()->role }})</span></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('projects.index') }}">Progetti</a></li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout
            </a>
        </li>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>
    @else
        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Accedi</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Registrati</a></li>
    @endauth
</ul>
