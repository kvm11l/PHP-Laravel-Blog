<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Wszystkie Kategorie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('posts.category', $category) }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition ease-in-out duration-150">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">{{ $category->name }}</h5>
                        <p class="font-normal text-gray-700">
                            Liczba postów: <span class="font-semibold text-indigo-600">{{ $category->posts_count }}</span>
                        </p>
                    </a>
                @endforeach
            </div>
            
            @if($categories->isEmpty())
                <div class="bg-white p-6 rounded-lg shadow text-center text-gray-500">
                    Brak utworzonych kategorii.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>