<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edytuj post') }}: {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 overflow-hidden shadow-sm sm:rounded-lg">
<form action="{{ route('posts.update', $post) }}" method="POST">
    @csrf
    @method('PATCH')

    <input type="hidden" name="return_url" value="{{ url()->previous() }}">
                    
                    <div class="mb-4">
                        <x-input-label for="title" :value="__('Tytuł')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $post->title)" required />
                    </div>

                    <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="category_id" :value="__('Zmień na istniejącą kategorię')" />
                                <select name="category_id" id="category_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="">-- Wybierz kategorię --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (old('category_id', $post->category_id) == $category->id) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="new_category" :value="__('LUB stwórz nową dla tego posta')" />
                                <x-text-input id="new_category" name="new_category" type="text" class="block mt-1 w-full" :value="old('new_category')" placeholder="Nowa nazwa..." />
                                <x-input-error :messages="$errors->get('new_category')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="content" :value="__('Treść')" />
                        <textarea name="content" rows="6" class="border-gray-300 rounded-md shadow-sm block mt-1 w-full" required>{{ old('content', $post->content) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end">
                        <x-primary-button>{{ __('Zapisz zmiany') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>