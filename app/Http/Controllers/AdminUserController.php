<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    // Wyświetlanie listy wszystkich zarejestrowanych osób
    public function index()
    {
        // Pobranie pełnej kolekcji rekordów z tabeli users
        $users = User::all();
        
        // Przekazanie danych do widoku panelu administracyjnego
        return view('admin.users.index', compact('users'));
    }

    // Nadawanie uprawnień administratora wybranemu użytkownikowi
    public function makeAdmin(User $user)
    {
        // Aktualizacja pola 'role' w bazie danych dla konkretnego modelu User
        $user->update(['role' => 'admin']);

        // Powrót do poprzedniego widoku z komunikatem potwierdzającym operację
        return back()->with('success', 'Pomyślnie nadano rolę administratora użytkownikowi ' . $user->name);
    }
}