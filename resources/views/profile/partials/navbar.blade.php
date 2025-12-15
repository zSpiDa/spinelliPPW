@auth

    Ciao, {{ Auth::user()->name }} ({{ Auth::user()->role }})


    




    Logout




@else

    Accedi


    Registrati

@endauth
