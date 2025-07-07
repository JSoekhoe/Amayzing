<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-serif font-semibold text-gray-800">
            Overzicht bestellingen
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flashmelding --}}
            @if(session('success'))
                <div class="px-4 py-3 bg-green-100 text-green-800 border border-green-200 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Bestellingen --}}
            @forelse ($orders as $order)
                <div class="bg-white rounded-2xl shadow p-6 border border-gray-200">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2 md:mb-0">
                            Bestelling #{{ $order->id }}
                        </h3>

                        {{-- Status badge --}}
                        <span class="text-sm font-medium px-3 py-1 rounded-full capitalize
                            {{ match($order->status) {
                                'afgerond' => 'bg-green-100 text-green-800',
                                'verzonden' => 'bg-yellow-100 text-yellow-800',
                                'geannuleerd' => 'bg-red-100 text-red-800',
                                default => 'bg-blue-100 text-blue-800',
                            } }}">
                            {{ str_replace('_', ' ', $order->status ?? 'in_behandeling') }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-700 space-y-1 mb-4">
                        <p><strong>Klant:</strong> {{ $order->name }}</p>
                        <p><strong>Type:</strong> {{ ucfirst($order->type) }}</p>
                        <p><strong>Totaalbedrag:</strong> €{{ number_format($order->total_price, 2, ',', '.') }}</p>
                    </div>

                    {{-- Acties --}}
                    <div class="flex flex-wrap gap-3 mt-4">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="inline-block text-sm bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                            Details bekijken
                        </a>

                        <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                              onsubmit="return confirm('Weet je zeker dat je deze bestelling wilt verwijderen?')"
                              class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-sm bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                                Verwijderen
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white p-6 rounded-xl shadow text-gray-600 text-center">
                    <p>Er zijn momenteel geen bestellingen.</p>
                </div>
            @endforelse

            {{-- Paginatie --}}
            <div class="pt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
