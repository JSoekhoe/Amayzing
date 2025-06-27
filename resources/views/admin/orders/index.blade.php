<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bestellingen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @forelse ($orders as $order)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-4">
                    <p><strong>#{{ $order->id }}</strong> - {{ $order->name }} - €{{ number_format($order->total_price, 2) }}</p>
                    <p>Status: <strong>{{ $order->status ?? 'in_behandeling' }}</strong></p>

                    <a href="{{ route('admin.orders.show', $order) }}" class="bg-blue-500 text-white px-3 py-1 rounded">Bekijken</a>

                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="inline-block" onsubmit="return confirm('Weet je zeker dat je deze bestelling wilt verwijderen?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded ml-2">Verwijderen</button>
                    </form>
                </div>
            @empty
                <div class="bg-white p-6 rounded shadow">
                    <p class="text-gray-700">Er zijn momenteel geen bestellingen.</p>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $orders->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
