<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-serif text-gray-800 tracking-tight">
            Bestellingen
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">

            @forelse ($orders as $order)
                <div class="bg-white shadow-md rounded-2xl p-6 mb-6 border border-gray-100">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-xl font-semibold text-gray-800">
                            Bestelling #{{ $order->id }}
                        </h3>
                        <span class="text-sm px-3 py-1 rounded-full bg-{{ $order->status === 'afgerond' ? 'green' : 'blue' }}-100 text-{{ $order->status === 'afgerond' ? 'green' : 'blue' }}-800 capitalize">
                            {{ $order->status ?? 'in_behandeling' }}
                        </span>
                    </div>

                    <p class="text-gray-600 text-sm mb-2">
                        Naam: <strong>{{ $order->name }}</strong><br>
                        Totaalbedrag: <strong>€{{ number_format($order->total_price, 2) }}</strong>
                    </p>

                    <div class="flex space-x-3 mt-4">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="inline-block bg-gray-900 text-white text-sm px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                            Bekijken
                        </a>

                        <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                              class="inline-block"
                              onsubmit="return confirm('Weet je zeker dat je deze bestelling wilt verwijderen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-500 text-white text-sm px-4 py-2 rounded-lg hover:bg-red-600 transition">
                                Verwijderen
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white p-6 rounded-xl shadow text-gray-600">
                    Er zijn momenteel geen bestellingen.
                </div>
            @endforelse

            <div class="mt-8">
                {{ $orders->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
