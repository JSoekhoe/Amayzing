<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-3xl font-semibold text-gray-900 leading-tight mb-12 tracking-tight">
            Producten
        </h1>
    </x-slot>

    <section class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-24 py-12 bg-gray-50 rounded-2xl shadow-lg">
        <div class="flex justify-end mb-10">
            <a href="{{ route('admin.products.create') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-8 py-3 rounded-full shadow-md transition">
                Nieuw product toevoegen
            </a>
        </div>

        @if(session('success'))
            <div class="mb-8 p-5 bg-gray-100 border border-gray-300 text-gray-700 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto rounded-xl shadow-xl bg-white">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-8 py-5 text-left text-sm font-serif font-semibold text-gray-700 uppercase tracking-wider">Foto</th>
                    <th class="px-8 py-5 text-left text-sm font-serif font-semibold text-gray-700 uppercase tracking-wider">Naam</th>
                    <th class="px-8 py-5 text-left text-sm font-serif font-semibold text-gray-700 uppercase tracking-wider">Prijs</th>
                    <th class="px-8 py-5 text-left text-sm font-serif font-semibold text-gray-700 uppercase tracking-wider">Pickup voorraad</th>
                    <th class="px-8 py-5 text-left text-sm font-serif font-semibold text-gray-700 uppercase tracking-wider">Bezorg voorraad</th>
                    <th class="px-8 py-5 text-left text-sm font-serif font-semibold text-gray-700 uppercase tracking-wider">Acties</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-300">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-8 py-4 whitespace-nowrap">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-16 w-16 object-cover rounded">
                            @else
                                <span class="text-gray-400 italic">Geen foto</span>
                            @endif
                        </td>
                        <td class="px-8 py-4 whitespace-nowrap text-gray-900 font-medium">{{ $product->name }}</td>
                        <td class="px-8 py-4 whitespace-nowrap text-gray-800">€ {{ number_format($product->price, 2, ',', '.') }}</td>
                        <td class="px-8 py-4 whitespace-nowrap text-gray-700">{{ $product->pickup_stock }}</td>
                        <td class="px-8 py-4 whitespace-nowrap text-gray-700">{{ $product->delivery_stock }}</td>
                        <td class="px-8 py-4 whitespace-nowrap space-x-4">
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="text-yellow-600 hover:text-yellow-900 font-semibold transition">
                                Bewerken
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Weet je zeker dat je dit product wilt verwijderen?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-900 font-semibold transition" type="submit">
                                    Verwijderen
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-8 text-center text-gray-400 italic">Geen producten gevonden.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-12">
            {{ $products->links() }}
        </div>
    </section>
</x-app-layout>
