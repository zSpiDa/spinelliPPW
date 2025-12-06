@auth

    Ciao, {{ Auth::user()->name }} ({{ Auth::user()->role }})


    Progetti




    Logout




@else

    Accedi


    Registrati

@endauth
