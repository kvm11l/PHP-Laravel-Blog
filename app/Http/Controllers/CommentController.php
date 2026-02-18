<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Zapisywanie nowego komentarza w bazie danych
    public function store(Request $request, Post $post)
    {
        // Sprawdzenie czy treść istnieje i mieści się w limitach znaków
        $request->validate([
            'content' => 'required|min:2|max:500',
        ]);

        // Tworzenie relacji: przypisanie komentarza do posta i zalogowanego usera
        $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        // Powrót na poprzednią stronę z komunikatem o sukcesie
        return back()->with('success', 'Komentarz został dodany.');
    }

    // Usuwanie komentarza z bazy
    public function destroy(Comment $comment)
    {
        // Weryfikacja uprawnień: Admin, autor komentarza lub właściciel posta mogą usuwać
        if (auth()->user()->role === 'admin' || auth()->id() === $comment->user_id || auth()->id() === $comment->post->user_id) {
            $comment->delete();
            return back()->with('success', 'Komentarz usunięty.');
        }

        // Jeśli warunki nie są spełnione, wyrzucenie błędu dostępu
        abort(403, 'Nie masz uprawnień do usunięcia tego komentarza.');
    }

    // Aktualizacja istniejącego komentarza (edycja)
    public function update(Request $request, Comment $comment)
    {
        // Blokada: edytować może wyłącznie osoba, która napisała komentarz
        if (auth()->id() !== $comment->user_id) {
            abort(403);
        }

        // Ponowna walidacja zmienionej treści
        $request->validate([
            'content' => 'required|min:2|max:500',
        ]);

        // Zapisanie zmian w modelu Comment
        $comment->update([
            'content' => $request->content,
        ]);

        return back()->with('success', 'Komentarz został zaktualizowany.');
    }
}