<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-3xl font-bold mb-4">{{ $post->title }}</h3>
                
                <p class="text-sm text-gray-500 mb-6">
                    Autor: 
                    <a href="{{ route('users.show', $post->user) }}" class="text-indigo-600 hover:underline font-bold">
                        {{ $post->user->name }}
                    </a> 
                    | Kategoria: 
                    @if($post->category)
                        <a href="{{ route('posts.category', $post->category) }}" class="text-indigo-600 hover:underline font-medium">
                            {{ $post->category->name }}
                        </a>
                    @else
                        <span class="italic text-gray-400">Brak</span>
                    @endif
                </p>

                <div class="text-gray-700 text-lg leading-relaxed mb-8">
                    @auth
                        {{ $post->content }}
                    @else
                        <p class="blur-sm select-none">Lorem ipsum dolor sit amet...</p>
                        <p class="text-center font-bold text-red-500">Zaloguj się, aby przeczytać pełną treść.</p>
                    @endauth
                </div>

                <hr class="my-8">

                <h4 class="text-xl font-bold mb-6">Komentarze ({{ $post->comments()->count() }})</h4>

                @auth
                    <div x-data="{ content: '', limit: 500 }" class="mb-8 bg-gray-50 p-4 rounded-lg">
                        <form action="{{ route('comments.store', $post) }}" method="POST">
                            @csrf
                            <x-input-label for="content" :value="__('Dodaj komentarz')" />
                            <textarea 
                                x-model="content"
                                name="content" 
                                id="content" 
                                rows="3" 
                                maxlength="500"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1"
                                placeholder="Co o tym sądzisz?"
                                required
                            ></textarea>
                            
                            <div class="flex justify-between items-center mt-2">
                                <div class="text-xs font-medium" :class="content.length > 450 ? 'text-red-500' : 'text-gray-500'">
                                    <span x-text="content.length"></span> / <span x-text="limit"></span> znaków
                                </div>
                                <x-primary-button>Opublikuj komentarz</x-primary-button>
                            </div>
                        </form>
                    </div>
                @endauth

                <div class="space-y-6">
                    @foreach($comments as $comment)
                        <div x-data="{ editing: false }" class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-bold text-sm">
                                        <a href="{{ route('users.show', $comment->user) }}" class="hover:text-indigo-600">
                                            {{ $comment->user->name }}
                                        </a>
                                    </span>
                                    <span class="text-xs text-gray-400 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                    
                                    @if($comment->created_at != $comment->updated_at)
                                        <span class="text-[10px] text-indigo-400 italic ml-2">
                                            (edytowano {{ $comment->updated_at->diffForHumans() }})
                                        </span>
                                    @endif
                                </div>
                                
                                @auth
                                    <div class="flex space-x-2">
                                        @if(auth()->id() === $comment->user_id)
                                            <button @click="editing = !editing" class="text-indigo-500 text-[10px] font-bold uppercase">Edytuj</button>
                                        @endif

                                        @if(auth()->user()->role === 'admin' || auth()->id() === $comment->user_id || auth()->id() === $post->user_id)
                                            <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 text-[10px] font-bold uppercase" onclick="return confirm('Usunąć?')">Usuń</button>
                                            </form>
                                        @endif
                                    </div>
                                @endauth
                            </div>

                            <div x-show="!editing" class="mt-2">
                                <p class="text-gray-700 text-sm">{{ $comment->content }}</p>
                            </div>

                            {{-- Tryb edycji --}}
                            <div x-show="editing" x-cloak class="mt-2" x-data="{ editContent: '{{ addslashes($comment->content) }}', limit: 500 }">
                                <form action="{{ route('comments.update', $comment) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <textarea 
                                        x-model="editContent"
                                        name="content" 
                                        maxlength="500"
                                        class="w-full border-gray-300 rounded-md shadow-sm text-sm" 
                                        rows="2"
                                    ></textarea>
                                    <div class="mt-2 flex justify-between items-center">
                                        <div class="text-[10px] text-gray-400">
                                            <span x-text="editContent.length"></span> / <span x-text="limit"></span>
                                        </div>
                                        <div class="space-x-2">
                                            <x-primary-button type="submit" class="py-1 px-2 text-xs text-[10px]">Zapisz</x-primary-button>
                                            <button type="button" @click="editing = false" class="text-[10px] text-gray-500 font-bold uppercase">Anuluj</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $comments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>