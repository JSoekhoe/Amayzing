<x-app-layout>
    <section class="min-h-screen bg-[#f7f6f4] flex items-center justify-center px-6 py-16">
        <div class="max-w-3xl bg-white rounded-3xl shadow-lg p-12 text-gray-800">
            <h1 class="text-4xl font-serif font-bold text-[#386641] mb-6">
                Bedankt voor je bestelling, {{ $order->name }}!
            </h1>
            <p class="text-lg mb-8 text-gray-700">
                We hebben je bestelling ontvangen en gaan deze zo snel mogelijk verwerken.
            </p>

            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Bestelgegevens</h2>
            <ul class="mb-8 space-y-2 text-gray-700">
                <li><strong>Naam:</strong> {{ $order->name }}</li>
                <li><strong>Email:</strong> {{ $order->email }}</li>
                <li><strong>Telefoon:</strong> {{ $order->phone }}</li>
                <li><strong>Type bestelling:</strong> {{ ucfirst($order->type) }}</li>
                @if($order->type === 'bezorgen')
                    <li><strong>Adres:</strong> {{ $order->address }}</li>
                    <li><strong>Postcode:</strong> {{ $order->postcode }}</li>
                @elseif($order->type === 'afhalen')
                    <li><strong>Afhaaltijd:</strong> {{ \Carbon\Carbon::parse($order->pickup_time)->format('H:i') }}</li>
                @endif
            </ul>

            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Bestelde producten</h2>
            <ul class="mb-8 space-y-2 text-gray-700">
                @foreach($order->items as $item)
                    <li>
                        {{ $item->product->name }} × {{ $item->quantity }} — &euro;{{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                    </li>
                @endforeach
            </ul>

            <p class="text-lg font-semibold text-gray-900 mb-12">
                Totaalprijs: &euro;{{ number_format($order->total_price, 2, ',', '.') }}
            </p>

            <p class="text-gray-700 mb-6">
                Heb je nog vragen? Je kunt ons bereiken via telefoon of e-mail.
            </p>

            <p class="text-gray-700">
                Met vriendelijke groet,<br>
                Het <span class="font-serif font-bold text-[#386641]">aMayzing</span> team
            </p>
        </div>
    </section>
</x-app-layout>
