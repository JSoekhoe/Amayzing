<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-800">Product aanmaken</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf

            @include('admin.products.form')

            <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Aanmaken
            </button>
        </form>
    </div>
</x-app-layout>
