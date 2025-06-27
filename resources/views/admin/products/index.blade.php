<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Producten</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('admin.products.create') }}" class="mb-4 inline-block bg-blue-500 text-white px-4 py-2 rounded">Nieuw product toevoegen</a>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Naam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prijs</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pickup voorraad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bezorg voorraad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acties</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">€ {{ number_format($product->price, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->pickup_stock }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->delivery_stock }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-yellow-600 hover:text-yellow-900">Bewerken</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Weet je zeker dat je dit product wilt verwijderen?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-900" type="submit">Verwijderen</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Geen producten gevonden.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
