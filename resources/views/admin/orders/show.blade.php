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

        {{-- Klantgegevens & Bestelinformatie --}}
        <div class="space-y-8">
            <h2 class="text-2xl font-serif font-semibold text-gray-900 border-b pb-2">Bestelinformatie</h2>
            <div class="grid sm:grid-cols-2 gap-6 text-gray-700">

                {{-- Klant --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Klantgegevens</h3>
                    <p>{{ $order->name }}</p>
                    <p class="text-sm text-gray-600">{{ $order->email }}</p>
                    <p class="text-sm text-gray-600">{{ $order->phone }}</p>
                </div>

                {{-- Type & Datum --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Type bestelling</h3>
                    <p>{{ ucfirst($order->type) }}</p>

                    @if($order->type === 'afhalen')
                        <p class="text-sm text-gray-600">
                            Datum: {{ $order->pickup_date ?? '-' }}<br>
                            Tijd: {{ $order->pickup_time ?? '-' }}
                        </p>
                    @else
                        <p class="text-sm text-gray-600">Bezorgdatum: {{ $order->delivery_date ?? '-' }}</p>
                    @endif
                </div>

                {{-- Status --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Status</h3>
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="mt-2 max-w-xs">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()"
                                class="w-full mt-1 rounded border border-gray-300 px-4 py-2 focus:ring-rose-400 focus:border-rose-400 shadow-sm transition">
                            @foreach(['pending', 'paid', 'onderweg', 'afgerond', 'geannuleerd'] as $status)
                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Betaling --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Betaling</h3>
                    @if($order->payment_id)
                        <p class="text-sm">Betaald: {{ $order->paid_at ? 'ja' : 'nee' }}</p>
                        @if($order->paid_at)
                            <p class="text-sm text-gray-600">Op: {{ \Carbon\Carbon::parse($order->paid_at)->format('d-m-Y H:i') }}</p>
                        @endif
                    @else
                        <p class="text-sm text-red-500">Nog niet betaald</p>
                    @endif
                </div>

                {{-- Notitie --}}
                @if($order->note)
                    <div class="sm:col-span-2">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Opmerking klant</h3>
                        <p class="text-gray-700">{{ $order->note }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Producten --}}
        <div class="space-y-6">
            <h2 class="text-2xl font-serif font-semibold text-gray-900 border-b pb-2">Producten</h2>
            <ul class="space-y-4">
                @foreach($order->items as $item)
                    <li class="flex justify-between items-center text-gray-800 border-b pb-2">
                        <span>{{ $item->quantity }}× {{ $item->product->name }}</span>
                        <span class="font-semibold">€{{ number_format($item->price * $item->quantity, 2, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="text-xl font-serif font-semibold text-gray-900 mt-6">
                Totaalprijs:
                <span class="text-rose-500">€{{ number_format($order->total_price, 2, ',', '.') }}</span>
            </div>
        </div>

        {{-- Bezorgadres (indien van toepassing) --}}
        @if($order->type === 'bezorgen' && $order->street && $order->postcode)
            <div class="space-y-4">
                <h2 class="text-2xl font-serif font-semibold text-gray-900 border-b pb-2">Bezorglocatie</h2>
                <p class="text-gray-700">
                    {{ $city }}
                </p>
            </div>
        @endif
    </section>
</x-app-layout>
