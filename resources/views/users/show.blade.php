<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Profil użytkownika: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-6 shadow-sm sm:rounded-lg border-b-4 border-indigo-500">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-sm text-gray-500 italic">Dołączył: {{ $user->created_at->format('d.m.Y') }} ({{ $user->created_at->diffForHumans() }})</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex space-x-8">
                        <div class="text-center">
                            <span class="block text-2xl font-bold text-indigo-600">{{ $user->posts()->count() }}</span>
                            <span class="text-xs uppercase text-gray-400 font-bold">Posty</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-2xl font-bold text-indigo-600">{{ $user->comments()->count() }}</span>
                            <span class="text-xs uppercase text-gray-400 font-bold">Komentarze</span>
                        </div>
                    </div>
                </div>

                @if($topCategories->isNotEmpty())
                    <div class="mt-6 pt-4 border-t">
                        <span class="text-sm font-semibold text-gray-700">Najczęstsze kategorie:</span>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($topCategories as $cat)
                                <a href="{{ route('posts.category', $cat) }}" class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs hover:bg-indigo-200 transition">
                                    {{ $cat->name }} ({{ $cat->posts_count }})
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg mb-6" x-data="{ showDates: {{ request('date_from') || request('date_to') ? 'true' : 'false' }} }">
                <form action="{{ route('users.show', $user) }}" method="GET" class="space-y-4">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-grow w-full">
                            <x-input-label for="search" :value="__('Szukaj w aktywności')" />
                            <x-text-input 
                                name="search" 
                                placeholder="{{ $tab === 'posts' ? 'Tytuł lub treść posta...' : 'Treść komentarza...' }}" 
                                value="{{ request('search') }}" 
                                class="w-full mt-1"
                            />
                        </div>

                        @if($tab === 'posts')
                            <div class="w-full md:w-64">
                                <x-input-label for="category_id" :value="__('Kategoria')" />
                                <select name="category_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm">
                                    <option value="">Wszystkie kategorie</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="toggle_user_dates" x-model="showDates" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="toggle_user_dates" class="ml-2 text-sm text-gray-600 cursor-pointer">
                            Filtruj według zakresu dat
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
                        @if(request('search') || request('category_id') || request('date_from') || request('date_to'))
                            <a href="{{ route('users.show', ['user' => $user, 'tab' => $tab]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase shadow-sm hover:bg-gray-50 transition">
                                {{ __('Wyczyść filtry') }}
                            </a>
                        @endif
                        
                        <x-primary-button>
                            {{ __('Filtruj') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
            
            <div class="flex space-x-4 border-b border-gray-200">
                <a href="{{ route('users.show', ['user' => $user, 'tab' => 'posts']) }}" 
                   class="py-2 px-4 text-sm font-medium {{ $tab === 'posts' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Posty użytkownika
                </a>
                <a href="{{ route('users.show', ['user' => $user, 'tab' => 'comments']) }}" 
                   class="py-2 px-4 text-sm font-medium {{ $tab === 'comments' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Komentarze użytkownika
                </a>
            </div>

            <div>
                @if($tab === 'posts')
                    <div class="space-y-6">
                        @forelse($posts as $post)
                            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                                <h3 class="text-xl font-bold">
                                    <a href="{{ route('posts.show', $post) }}" class="hover:text-indigo-600 transition">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                
                                <div class="flex items-center space-x-4 text-xs text-gray-400 mb-4">
                                    {{-- Kategoria --}}
                                    <span>
                                        Kategoria: 
                                        @if($post->category)
                                            <a href="{{ route('posts.category', $post->category) }}" class="text-indigo-600 hover:underline font-medium">
                                                {{ $post->category->name }}
                                            </a>
                                        @else
                                            <span class="text-gray-400 italic">Brak</span>
                                        @endif
                                    </span>
                                    
                                    <span>|</span>
                                    
                                    {{-- Data --}}
                                    <span>{{ $post->created_at->diffForHumans() }}</span>
                                    
                                    <span>|</span>
                                    
                                    {{-- LICZBA KOMENTARZY --}}
                                    <a href="{{ route('posts.show', $post) }}#comments" class="flex items-center hover:text-indigo-500 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        {{ $post->comments_count ?? $post->comments()->count() }} {{ trans_choice('komentarz|komentarze|komentarzy', $post->comments()->count()) }}
                                    </a>
                                </div>
                                
                                {{-- TREŚĆ / BLOKADA --}}
                                <div class="text-gray-600 text-sm">
                                    @auth
                                        {{ Str::limit($post->content, 200) }}
                                        @if(strlen($post->content) > 200)
                                            <a href="{{ route('posts.show', $post) }}" class="text-indigo-500 hover:underline font-bold">...czytaj więcej</a>
                                        @endif
                                        
                                        <div class="mt-4">
                                            <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-md text-xs font-bold hover:bg-indigo-100 transition uppercase tracking-widest">
                                                Przejdź do posta →
                                            </a>
                                        </div>
                                    @else
                                        <div class="relative">
                                            <p class="blur-sm select-none">
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                            </p>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="bg-white/80 p-3 rounded border border-gray-100 shadow-sm text-center">
                                                    <p class="text-xs font-medium text-gray-600 mb-2">Zaloguj się, aby zobaczyć treść</p>
                                                    <a href="{{ route('login') }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded text-[10px] font-bold uppercase tracking-tighter hover:bg-indigo-700 transition">
                                                        Zaloguj się
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-500 rounded-md text-xs font-bold uppercase tracking-widest">
                                                Przejdź do posta →
                                            </a>
                                        </div>
                                    @endauth
                                </div>

                                {{-- Edycja lub usuniecie posta --}}
                                <div class="mt-4 flex space-x-4 border-t pt-4">
                                    @if(auth()->id() === $post->user_id)
                                        <a href="{{ route('posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">
                                            Edytuj post
                                        </a>
                                    @endif

                                    @if(auth()->id() === $post->user_id || (auth()->check() && auth()->user()->role === 'admin'))
                                        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć ten post?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-semibold">
                                                Usuń post
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-10">Użytkownik nie dodał jeszcze żadnych postów.</p>
                        @endforelse
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-bold">
                                <tr>
                                    <th class="px-6 py-3 text-left">Treść komentarza</th>
                                    <th class="px-6 py-3 text-left">Pod postem</th>
                                    <th class="px-6 py-3 text-right">Akcje</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                @forelse($comments as $comment)
                                    <tr x-data="{ editing: false }">
                                        <td class="px-6 py-4">
                                            <div x-show="!editing">
                                                <p class="text-gray-700">{{ $comment->content }}</p>
                                                <p class="text-[10px] text-gray-400 mt-1">
                                                    {{ $comment->created_at->format('d.m.Y H:i') }}
                                                    @if($comment->created_at != $comment->updated_at)
                                                        <span class="text-indigo-400 italic">(edytowano {{ $comment->updated_at->diffForHumans() }})</span>
                                                    @endif
                                                </p>
                                            </div>

                                            <div x-show="editing" x-cloak class="mt-2">
                                                <form action="{{ route('comments.update', $comment) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <textarea name="content" class="w-full border-gray-300 rounded-md shadow-sm text-sm" rows="2">{{ $comment->content }}</textarea>
                                                    <div class="mt-2 flex space-x-2">
                                                        <x-primary-button type="submit" class="py-1 px-2 text-[10px]">Zapisz</x-primary-button>
                                                        <button type="button" @click="editing = false" class="text-[10px] text-gray-500 font-bold uppercase">Anuluj</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('posts.show', $comment->post) }}" class="text-indigo-600 hover:underline font-medium">
                                                {{ $comment->post->title }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end space-x-3">
                                                @auth
                                                    @if(auth()->id() === $comment->user_id)
                                                        <button @click="editing = !editing" class="text-indigo-500 hover:text-indigo-700 font-bold uppercase text-[10px]">
                                                            Edytuj
                                                        </button>
                                                    @endif

                                                    @if(auth()->id() === $comment->user_id || auth()->user()->role === 'admin')
                                                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Usunąć ten komentarz?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold uppercase text-[10px]">
                                                                Usuń
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endauth
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-6 py-10 text-center text-gray-500">Brak komentarzy pasujących do filtrów.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="p-4 border-t">
                            {{ $comments->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>