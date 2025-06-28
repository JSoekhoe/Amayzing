<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-serif text-gray-800 leading-tight">
            Beheerdersdashboard
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-6xl mx-auto px-6 lg:px-12">
            <div class="bg-white shadow-md rounded-2xl p-8 border border-gray-100">
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">Welkom, admin 👋</h3>
                <p class="text-gray-600 mb-8">
                    Kies hieronder een sectie om te beheren.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <a href="{{ route('admin.orders.index') }}"
                       class="block bg-gray-900 text-white text-lg px-6 py-4 rounded-xl hover:bg-gray-800 transition shadow text-center font-medium">
                        📦 Bestellingen beheren
                    </a>

                    <a href="{{ route('admin.products.index') }}"
                       class="block bg-gray-700 text-white text-lg px-6 py-4 rounded-xl hover:bg-gray-600 transition shadow text-center font-medium">
                        🧁 Producten beheren
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
