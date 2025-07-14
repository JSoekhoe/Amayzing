<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Betaling</h1>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-white border border-gray-200 rounded-3xl shadow-md p-10 text-center">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Bevestig je betaling</h2>

            <p class="text-lg text-gray-600 mb-6">
                Te betalen bedrag:
                <span class="text-2xl font-bold text-gray-900">€{{ number_format($amount, 2, ',', '.') }}</span>
            </p>

            <form method="POST" action="{{ route('payment.process') }}">
                @csrf
                <input type="hidden" name="amount" value="{{ number_format($amount, 2, '.', '') }}">
                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <button type="submit"
                        class="w-full bg-gray-800 hover:bg-gray-700 text-white font-semibold py-3 rounded-full shadow-md transition duration-200 focus:outline-none focus:ring-4 focus:ring-gray-300">
                    Betaal nu
                </button>
            </form>

            @if(session('error'))
                <div class="text-red-600 mt-4 text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
