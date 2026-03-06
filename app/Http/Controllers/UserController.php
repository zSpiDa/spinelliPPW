<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
     // mostra la lista degli utenti
     $users = User::all();
     return view('users.index', compact('users'));
    }

    public function edit()
    {
        // mostra il form per modificare il profilo dell'utente
        $user = auth()->user();
        return view('users.edit', compact('user'));
    }

    public function update(Request $request)
    {
        //aggiorna il profilo dell'utente
        $user = auth()->user();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:pi,manager,researcher,collaborator'],
        ]);
        $user->update($request->all());
        return redirect()->route('users.edit')->with('status', 'Profilo aggiornato con successo!');
    }

    public function destroy()
    {
        //elimina l'account dell'utente
        $user = auth()->user();
        $user->delete();
        return redirect()->route('home')->with('status', 'Account eliminato con successo!');
    }
    
}
