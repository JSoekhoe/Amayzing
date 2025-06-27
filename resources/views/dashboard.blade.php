<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Beheerdersdashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Welkom, admin!</h3>
                    <p class="mb-6">Kies hieronder een beheerpagina:</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('admin.orders.index') }}"
                           class="block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-5 rounded text-center">
                            Bestellingen beheren
                        </a>

                        <a href="{{ route('admin.products.index') }}"
                           class="block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-5 rounded text-center">
                            Producten beheren
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
