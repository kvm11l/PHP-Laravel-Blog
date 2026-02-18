<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Wyświetlanie strony głównej z filtrowaniem i wyszukiwarką
    public function index(Request $request): View
    {
        // Pobieranie parametrów sortowania i filtrów z adresu URL
        $sort = $request->get('sort', 'desc');
        $cSort = $request->get('c_sort', 'desc');
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $categoryId = $request->get('category_id');

        // Pobranie kategorii do listy rozwijanej w wyszukiwarce
        $categories = Category::orderBy('name')->get();

        // Budowanie zapytania z eager loadingiem relacji i warunkami 'when'
        $posts = Post::with(['user', 'category', 'comments' => function($query) use ($cSort) {
            $query->orderBy('created_at', $cSort);
        }, 'comments.user'])
        ->when($search, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        })
        ->when($categoryId, function ($query, $categoryId) {
            return $query->where('category_id', $categoryId);
        })
        ->when($dateFrom, function ($query, $dateFrom) {
            return $query->whereDate('created_at', '>=', $dateFrom);
        })
        ->when($dateTo, function ($query, $dateTo) {
            return $query->whereDate('created_at', '<=', $dateTo);
        })
        ->orderBy('created_at', $sort)
        ->paginate(5)
        ->withQueryString(); // Zachowanie filtrów przy zmianie stron

        return view('posts.index', compact('posts', 'sort', 'cSort', 'search', 'dateFrom', 'dateTo', 'categories', 'categoryId'));
    }

    // Wyświetlanie formularza tworzenia posta
    public function create()
    {
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    // Zapisywanie nowego posta z obsługą nowej kategorii
    public function store(Request $request)
    {
        // Walidacja pól (wymaga albo istniejącej kategorii, albo nazwy nowej)
        $validated = $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required_without:new_category|nullable|exists:categories,id',
            'new_category' => 'nullable|max:50|unique:categories,name',
            'content' => 'required',
        ]);

        // Automatyczne tworzenie nowej kategorii, jeśli została wpisana
        if (!empty($request->new_category)) {
            $category = \App\Models\Category::create([
                'name' => $request->new_category,
                'slug' => \Illuminate\Support\Str::slug($request->new_category)
            ]);
            $categoryId = $category->id;
        } else {
            $categoryId = $request->category_id;
        }

        // Utworzenie posta przypisanego do zalogowanego użytkownika
        \App\Models\Post::create([
            'user_id' => auth()->id(),
            'category_id' => $categoryId,
            'title' => $validated['title'],
            'slug' => \Illuminate\Support\Str::slug($validated['title']),
            'content' => $validated['content'],
        ]);

        return redirect()->route('home')->with('success', 'Post dodany pomyślnie!');
    }

    // Usuwanie posta i sprzątanie pustych kategorii
    public function destroy(Post $post)
    {
        // Weryfikacja uprawnień (tylko autor lub admin)
        if (auth()->id() !== $post->user_id && auth()->user()->role !== 'admin') {
            return back()->with('error', 'Nie masz uprawnień do usunięcia tego posta.');
        }

        $categoryId = $post->category_id;
        $post->delete();

        // Jeśli kategoria stała się pusta po usunięciu posta - usuń ją
        if ($categoryId) {
            $otherPostsExists = \App\Models\Post::where('category_id', $categoryId)->exists();
            if (!$otherPostsExists) {
                \App\Models\Category::where('id', $categoryId)->delete();
            }
        }

        return back()->with('success', 'Post został usunięty.');
    }

    // Wyświetlanie formularza edycji (tylko dla autora)
    public function edit(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403, 'Tylko autor może edytować ten post.');
        }

        $categories = \App\Models\Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    // Aktualizacja posta z inteligentnym powrotem na poprzednią stronę
    public function update(Request $request, Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required_without:new_category|nullable|exists:categories,id',
            'new_category' => 'nullable|max:50|unique:categories,name',
            'content' => 'required',
        ]);

        $oldCategoryId = $post->category_id;

        // Logika zmiany/tworzenia kategorii podczas edycji
        if (!empty($request->new_category)) {
            $category = \App\Models\Category::create([
                'name' => $request->new_category,
                'slug' => \Illuminate\Support\Str::slug($request->new_category)
            ]);
            $categoryId = $category->id;
        } else {
            $categoryId = $request->category_id;
        }

        $post->update([
            'title' => $validated['title'],
            'category_id' => $categoryId,
            'content' => $validated['content'],
            'slug' => \Illuminate\Support\Str::slug($validated['title']),
        ]);

        // Sprzątanie starej kategorii, jeśli została porzucona
        if ($oldCategoryId && $oldCategoryId != $categoryId) {
            $otherPostsExist = \App\Models\Post::where('category_id', $oldCategoryId)->exists();
            if (!$otherPostsExist) {
                \App\Models\Category::where('id', $oldCategoryId)->delete();
            }
        }

        // Przekierowanie na adres zapisany w ukrytym polu (np. profil użytkownika)
        if ($request->has('return_url')) {
            return redirect($request->return_url)->with('success', 'Post został zaktualizowany.');
        }

        return redirect()->route('home')->with('success', 'Post został zaktualizowany.');
    }

    // Wyświetlanie pojedynczego posta z jego komentarzami
    public function show(Request $request, Post $post)
    {
        $sort = $request->get('sort', 'desc');
        
        $comments = $post->comments()
            ->with('user')
            ->orderBy('created_at', $sort)
            ->paginate(10)
            ->withQueryString();

        return view('posts.show', compact('post', 'comments', 'sort'));
    }

    // Filtrowanie postów według konkretnej kategorii
    public function category(Request $request, Category $category): View
    {
        $sort = $request->get('sort', 'desc');
        $cSort = $request->get('c_sort', 'desc');
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $categories = Category::orderBy('name')->get();

        $posts = Post::where('category_id', $category->id)
            ->with(['user', 'category', 'comments' => function($query) use ($cSort) {
                $query->orderBy('created_at', $cSort);
            }, 'comments.user'])
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($dateFrom, function ($query, $dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderBy('created_at', $sort)
            ->paginate(5)
            ->withQueryString();

        return view('posts.index', compact('posts', 'sort', 'cSort', 'search', 'dateFrom', 'dateTo', 'category', 'categories'));
    }

    // Wyświetlanie spisu kategorii wraz z licznikiem postów
    public function categoriesIndex(): View
    {
        $categories = Category::withCount('posts')
            ->orderBy('name', 'asc')
            ->get();

        return view('categories.index', compact('categories'));
    }

    // Widok profilu publicznego użytkownika z podziałem na zakładki
    public function userProfile(Request $request, \App\Models\User $user)
    {
        $tab = $request->get('tab', 'posts');
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $categoryId = $request->get('category_id');

        // Obliczanie statystyk topowych kategorii dla danego usera
        $topCategories = \App\Models\Category::whereHas('posts', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->withCount(['posts' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->orderBy('posts_count', 'desc')
            ->take(3)
            ->get();

        $categories = \App\Models\Category::orderBy('name')->get();

        // Pobieranie postów usera z uwzględnieniem filtrów profilu
        $posts = $user->posts()
            ->with(['category', 'user'])
            ->when($search, function ($query, $search) {
                return $query->where(fn($q) => $q->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%"));
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->paginate(5, ['*'], 'p_page')
            ->withQueryString();

        // Pobieranie komentarzy usera z uwzględnieniem filtrów profilu
        $comments = $user->comments()
            ->with('post')
            ->when($search, function ($query, $search) {
                return $query->where('content', 'like', "%{$search}%");
            })
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->paginate(10, ['*'], 'c_page')
            ->withQueryString();

        return view('users.show', compact('user', 'posts', 'comments', 'topCategories', 'tab', 'search', 'dateFrom', 'dateTo', 'categoryId', 'categories'));
    }
}