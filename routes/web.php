<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

// 1. Trasy statyczne (ogólnodostępne) - widoczne dla każdego odwiedzającego
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/category/{category:slug}', [PostController::class, 'category'])->name('posts.category');
Route::get('/categories', [PostController::class, 'categoriesIndex'])->name('categories.index');
Route::get('/user/{user}', [PostController::class, 'userProfile'])->name('users.show');

// 2. Trasy chronione (wymagają logowania) - middleware 'auth' sprawdza sesję użytkownika
Route::middleware('auth')->group(function () {
    // Zarządzanie postami (tworzenie, edycja, usuwanie)
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create'); 
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Zarządzanie komentarzami (CRUD dla zalogowanych)
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');

    // Zarządzanie własnym profilem i ustawieniami konta
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// 3. Trasy z parametrami dynamicznymi 
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show'); 

// 4. Panel Admina - wymaga logowania oraz roli administratora
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/panel', function () {
        return "Witaj w tajnym panelu administratora!";
    })->name('admin.panel');

    // Zarządzanie użytkownikami z poziomu panelu
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::patch('/users/{user}/make-admin', [AdminUserController::class, 'makeAdmin'])->name('admin.users.makeAdmin');
});

// Ładowanie dodatkowych tras uwierzytelniania (login, register itd.)
require __DIR__.'/auth.php';