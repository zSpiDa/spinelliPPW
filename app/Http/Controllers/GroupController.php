<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GroupController extends Controller
{
    //Controller per gestire il gruppo di ricerca
    public function show()
    {
        // Logica per visualizzare i gruppi di ricerca
        $group = \App\Models\Group::with(['users'])->first();
        $users = \App\Models\User::all();
        return view('groups.show', ['group' => $group, 'users' => $users]);
    }
    

    public function update(Request $request)
    {
        // Logica per aggiornare i gruppi di ricerca
        $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string']);
        $group = \App\Models\Group::first();
        $group->update($request->only('name', 'description'));
        return redirect()->route('groups.show')->with('success', 'Gruppo aggiornato.');
    }

    public function addMember(Request $request)
    {
        // Logica per aggiungere un membro al gruppo di ricerca
        $request->validate(['user_id' => 'required|exists:users,id']);
        $group = \App\Models\Groups::first();
        $user = \App\Models\User::find($request->user_id);
        $user->group_id = $group->id;
        $user->save();
        return redirect()->route('groups.show')->with('success', 'Membro aggiunto al gruppo.');
    }

    public function removeMember(Request $request, $userId)
    {
        // Logica per rimuovere un membro dal gruppo di ricerca
        $group = \App\Models\Groups::first();
        $user = \App\Models\User::find($userId);
        if ($user->group_id == $group->id) {
            $user->group_id = null;
            $user->save();
            return redirect()->route('groups.show')->with('success', 'Membro rimosso dal gruppo.');
        }
        return redirect()->route('groups.show')->with('error', 'Membro non trovato nel gruppo.');
    }

    public function syncMembers(Request $request)
    {
        // Logica per sincronizzare i membri del gruppo di ricerca
        $request->validate(['user_ids' => 'required|array', 'user_ids.*' => 'exists:users,id']);
        $group = \App\Models\Groups::first();
        \App\Models\User::where('group_id', $group->id)->update(['group_id' => null]); // Rimuove tutti i membri attuali
        \App\Models\User::whereIn('id', $request->user_ids)->update(['group_id' => $group->id]); // Aggiunge i nuovi membri
        return redirect()->route('groups.show')->with('success', 'Membri sincronizzati.');
    }
    
}
