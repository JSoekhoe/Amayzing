<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-serif font-semibold text-gray-900 tracking-wide mb-6">
            Bestelling #{{ $order->id }}
        </h1>
    </x-slot>

    {{-- Succesmelding --}}
    @if(session('success'))
        <div class="max-w-5xl mx-auto px-6 pt-6">
            <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 border border-green-200 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <section class="max-w-5xl mx-auto px-6 py-12 bg-white rounded-lg shadow-lg space-y-12">

        {{-- Klant & Bestelinformatie --}}
        <div class="space-y-8">
            <h2 class="text-2xl font-serif font-semibold text-gray-900 border-b pb-2">Bestelinformatie</h2>
            <div class="grid sm:grid-cols-2 gap-6 text-gray-700">

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Klantgegevens</h3>
                    <p class="text-base">
                        {{ $order->name }}<br>
                        <span class="text-sm text-gray-600">{{ $order->email }}</span>
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Type bestelling</h3>
                    <p class="text-base">{{ ucfirst($order->type) }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Status</h3>
                    {{-- Wijzig status --}}
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="mt-2 max-w-xs">
                        @csrf
                        @method('PATCH')
                        <select name="status" id="status" onchange="this.form.submit()"
                                class="w-full mt-1 rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-rose-400 shadow-sm transition">
                            @foreach(['in_behandeling', 'onderweg', 'afgerond', 'geannuleerd'] as $status)
                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>

        {{-- Producten --}}
        <div class="space-y-6">
            <h2 class="text-2xl font-serif font-semibold text-gray-900 border-b pb-2">Producten</h2>

            <ul class="space-y-4">
                @foreach($order->items as $item)
                    <li class="flex justify-between items-center text-gray-800 border-b pb-2">
                        <span class="text-base">
                            {{ $item->quantity }}× {{ $item->product->name }}
                        </span>
                        <span class="font-semibold text-gray-900">
                            €{{ number_format($item->price, 2, ',', '.') }}
                        </span>
                    </li>
                @endforeach
            </ul>

            <div class="text-xl font-serif font-semibold text-gray-900 mt-6">
                Totaalprijs:
                <span class="text-rose-500">
                    €{{ number_format($order->total_price, 2, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Bezorglocatie --}}
        @if($order->type === 'bezorgen' && $order->street && $order->postcode)
            <div class="space-y-4">
                <h2 class="text-2xl font-serif font-semibold text-gray-900 border-b pb-2">Bezorglocatie</h2>
                <p class="text-gray-700 text-base">
                    {{ $order->street }} {{ $order->housenumber }}{{ $order->addition ? ' ' . $order->addition : '' }}<br>
                    {{ $order->postcode }}
                </p>
            </div>
        @endif

    </section>
</x-app-layout>
