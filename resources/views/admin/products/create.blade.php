<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-3xl font-semibold text-gray-900 mb-10">
            Product aanmaken
        </h1>
    </x-slot>

    <div class="max-w-3xl mx-auto py-10 px-6 sm:px-12 lg:px-24 bg-gray-50 rounded-2xl shadow-lg">
        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf

            @include('admin.products.form')

            <button type="submit"
                    class="w-full bg-gray-800 text-white font-semibold py-3 rounded-full hover:bg-gray-900 transition">
                Aanmaken
            </button>
        </form>
    </div>
</x-app-layout>
