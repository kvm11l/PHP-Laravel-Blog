<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Najnowsze wpisy') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-6 shadow-sm sm:rounded-lg" x-data="{ showDates: {{ request('date_from') || request('date_to') ? 'true' : 'false' }} }">
                <form action="{{ route('home') }}" method="GET" class="space-y-4">
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="c_sort" value="{{ $cSort }}">

                        <div class="flex flex-col lg:flex-row gap-4 items-end">
                            <div class="flex-grow w-full">
                                <x-input-label for="search" :value="__('Wyszukaj posty')" />
                                <x-text-input 
                                    name="search" 
                                    placeholder="Tytuł lub treść..." 
                                    value="{{ request('search') }}" 
                                    class="w-full mt-1"
                                />
                            </div>

                            <div class="w-full lg:w-64">
                                <x-input-label for="category_id" :value="__('Kategoria')" />
                                <select name="category_id" class="w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <option value="">Wszystkie kategorie</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ (isset($categoryId) && $categoryId == $cat->id) || (isset($category) && $category->id == $cat->id) ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-center bg-gray-50 rounded-md border px-3 h-[42px] shrink-0">
                                <span class="text-xs font-semibold text-gray-500 uppercase mr-3">Sortuj:</span>
                                <a href="{{ route('home', array_merge(request()->query(), ['sort' => 'desc'])) }}" 
                                class="text-sm px-2 {{ $sort === 'desc' ? 'text-indigo-600 font-bold' : 'text-gray-400 hover:text-gray-600' }}">
                                    Najnowsze
                                </a>
                                <div class="w-px h-4 bg-gray-300 mx-1"></div>
                                <a href="{{ route('home', array_merge(request()->query(), ['sort' => 'asc'])) }}" 
                                class="text-sm px-2 {{ $sort === 'asc' ? 'text-indigo-600 font-bold' : 'text-gray-400 hover:text-gray-600' }}">
                                    Najstarsze
                                </a>
                            </div>
                        </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="toggle_dates" x-model="showDates" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="toggle_dates" class="ml-2 text-sm text-gray-600 cursor-pointer">
                            Filtruj według daty publikacji
                        </label>
                    </div>

                    <div x-show="showDates" x-cloak
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-md border border-dashed border-gray-300">
                        <div>
                            <x-input-label for="date_from" :value="__('Data od')" />
                            <x-text-input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full mt-1" />
                        </div>
                        <div>
                            <x-input-label for="date_to" :value="__('Data do')" />
                            <x-text-input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full mt-1" />
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-2 border-t pt-4">
                        @if(request('search') || request('date_from') || request('date_to'))
                            <a href="{{ route('home', ['sort' => $sort, 'c_sort' => $cSort]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase shadow-sm hover:bg-gray-50 transition">
                                {{ __('Wyczyść') }}
                            </a>
                        @endif
                        <x-primary-button>
                            {{ __('Zastosuj filtry') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                @if($posts->isEmpty())
                    <div class="bg-white p-6 rounded-lg shadow text-center text-gray-500">
                        Nie znaleziono żadnych postów.
                    </div>
                @endif

                @isset($category)
                    <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mb-6 shadow-sm rounded-r-lg">
                        <div class="flex items-center justify-between">
                            <p class="text-indigo-700">
                                Wyświetlasz posty z kategorii: <span class="font-bold">{{ $category->name }}</span>
                            </p>
                            {{-- Link prowadzący do spisu wszystkich kategorii --}}
                            <a href="{{ route('categories.index') }}" class="text-xs text-indigo-500 hover:text-indigo-800 underline uppercase font-bold">
                                Pokaż wszystkie kategorie
                            </a>
                        </div>
                    </div>
                @endisset
                @foreach($posts as $post)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-2xl font-bold mb-2">
                            <a href="{{ route('posts.show', $post) }}" class="hover:text-indigo-600">
                                {{ $post->title }}
                            </a>
                        </h3>
                        <p class="text-sm text-gray-500 mb-4">
                            Autor: 
                            <a href="{{ route('users.show', $post->user) }}" class="text-indigo-600 hover:underline font-bold">
                            {{ $post->user->name }}
                            </a>
                            Kategoria: 
                            @if($post->category)
                                <a href="{{ route('posts.category', $post->category) }}" class="text-indigo-600 hover:underline font-medium">
                                    {{ $post->category->name }}
                                </a>
                            @else
                                <span class="italic text-gray-400">Brak</span>
                            @endif
                        </p>

                        <div class="text-xs text-gray-400 mb-4">
                            <p>Utworzono: {{ $post->created_at->format('d.m.Y H:i') }} ({{ $post->created_at->diffForHumans() }})</p>
                            
                            @if($post->created_at != $post->updated_at)
                                <p class="text-indigo-500 font-medium">
                                    Zaktualizowano: {{ $post->updated_at->format('d.m.Y H:i') }} ({{ $post->updated_at->diffForHumans() }})
                                </p>
                            @endif
                        </div>

                        @auth
                            <div class="text-gray-700 leading-relaxed">
                                {{ Str::limit($post->content, 300) }} 
                                @if(strlen($post->content) > 300)
                                    <a href="{{ route('posts.show', $post) }}" class="text-indigo-500 font-bold">...czytaj więcej</a>
                                @endif
                            </div>
                        @endauth

                        @auth
                            <div class="mt-4 flex space-x-4 border-t pt-4">
                                {{-- Edycja posta – tylko właściciel --}}
                                @if(auth()->id() === $post->user_id)
                                    <a href="{{ route('posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">
                                        Edytuj post
                                    </a>
                                @endif

                                {{-- Usuwanie posta – właściciel lub admin --}}
                                @if(auth()->id() === $post->user_id || auth()->user()->role === 'admin')
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć ten post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-semibold">
                                            Usuń post
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- KOMENTARZE (Podgląd) --}}
                            <div class="mt-8 border-t pt-6 bg-gray-50 p-4 rounded-b-lg">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-lg font-bold">
                                        Ostatnie komentarze ({{ $post->comments->count() }})
                                    </h4>
                                    
                                    {{-- Przełącznik sortowania komentarzy --}}
                                    <div class="flex space-x-2 text-[10px] uppercase tracking-widest font-bold">
                                        <span class="text-gray-400">Sortuj:</span>
                                        <a href="{{ route('home', array_merge(request()->query(), ['c_sort' => 'desc'])) }}" 
                                        class="{{ $cSort === 'desc' ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
                                            Najnowsze
                                        </a>
                                        <a href="{{ route('home', array_merge(request()->query(), ['c_sort' => 'asc'])) }}" 
                                        class="{{ $cSort === 'asc' ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
                                            Najstarsze
                                        </a>
                                    </div>
                                </div>

{{-- Lista komentarzy (podgląd 5 ostatnich) --}}
<div class="space-y-4 mb-4">
    @foreach($post->comments->take(5) as $comment)
        <div x-data="{ editing: false }" class="bg-white p-3 rounded shadow-sm border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <span class="font-bold text-sm">{{ $comment->user->name }}</span>
                    <span class="text-xs text-gray-400 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                
                <div class="flex space-x-2">
                    @auth
                        {{-- Przycisk edycji - tylko dla autora --}}
                        @if(auth()->id() === $comment->user_id)
                            <button @click="editing = !editing" class="text-indigo-500 text-[10px] font-bold uppercase">Edytuj</button>
                        @endif

                        {{-- Przycisk usuwania --}}
                        @if(auth()->user()->role === 'admin' || auth()->id() === $comment->user_id || auth()->id() === $post->user_id)
                            <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-[10px] font-bold uppercase" onclick="return confirm('Usunąć?')">Usuń</button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Tryb wyświetlania --}}
            <div x-show="!editing">
                <p class="text-gray-700 text-sm mt-1">{{ $comment->content }}</p>
                
                {{-- Informacja o edycji (kiedy) --}}
                @if($comment->created_at != $comment->updated_at)
                    <p class="text-[10px] text-gray-400 italic mt-1">
                        (edytowano {{ $comment->updated_at->diffForHumans() }})
                    </p>
                @endif
            </div>

            {{-- Tryb edycji (w locie) --}}
            <div x-show="editing" x-cloak class="mt-2">
                <form action="{{ route('comments.update', $comment) }}" method="POST">
                    @csrf @method('PATCH')
                    <textarea name="content" class="w-full border-gray-300 rounded-md shadow-sm text-sm" rows="2">{{ $comment->content }}</textarea>
                    <div class="mt-2 flex space-x-2">
                        <button type="submit" class="bg-indigo-600 text-white px-2 py-1 rounded text-[10px] font-bold uppercase hover:bg-indigo-700">
                            Zapisz
                        </button>
                        <button type="button" @click="editing = false" class="text-[10px] text-gray-500 font-bold uppercase">
                            Anuluj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</div>

{{-- Dodawanie komentarza bezpośrednio na stronie głównej --}}
<form action="{{ route('comments.store', $post) }}" method="POST" class="mt-4 border-t pt-4">
    @csrf
    <textarea name="content" rows="1" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500" placeholder="Dodaj szybki komentarz..." required></textarea>
    <x-primary-button class="mt-2 text-[10px]">Wyślij</x-primary-button>
</form>

                                <div class="mt-4">
                                    <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center text-indigo-600 font-bold hover:text-indigo-900 transition">
                                        @if($post->comments->count() > 5)
                                            Zobacz wszystkie komentarze ({{ $post->comments->count() }}) →
                                        @else
                                            Przejdź do posta →
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endauth

                        @guest
                            <div class="relative mb-8">
                                <p class="text-gray-400 select-none blur-sm">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua...
                                </p>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="bg-white/80 p-4 rounded-lg border border-gray-200 shadow-sm text-center">
                                        <p class="text-sm font-medium text-gray-600 mb-2">Treść dostępna tylko dla zalogowanych użytkowników.</p>
                                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                            Zaloguj się, aby przeczytać
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endguest
                    </div>
                @endforeach
            </div>

            {{-- PAGINACJA POSTÓW --}}
            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>