<x-app-layout>
    <div class="text-center py-12">
        <h1 class="text-3xl text-red-600 font-bold">Betaling mislukt of geannuleerd</h1>
        <p class="mt-4">Status: {{ $order->status }}</p>
        <p class="mt-2">Probeer het opnieuw.</p>
        <a href="{{ route('checkout', $order->id) }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded">Opnieuw proberen</a>
    </div>
</x-app-layout>
