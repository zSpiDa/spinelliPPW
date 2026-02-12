<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function store (Request $request, Project $project)
    {
        $request->validate(['body' => 'required|max:1000']);

        // Creiamo il commento collegato al progetto
        $project->comments()->create([
            'body' => $request->body,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Commento aggiunto!');
    }

    public function destroy(Comment $comment)
    {
        // Controllo che solo l'autore possa cancellare
        if (Auth::id() !== $comment->user_id) {
            abort(403, 'Non autorizzato');
        }

        $comment->delete();
        return back()->with('success', 'Commento eliminato.');
    }
}
