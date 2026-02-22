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
            @if($order->type === 'bezorgen' && $order->street && $order->postcode)
                <p><strong>Adres:</strong> {{ $order->street }} {{ $order->housenumber }}, {{ $order->postcode }} {{ $order->city ?? '' }}</p>
            @endif

            <p><strong>Telefoon:</strong> {{ $order->phone }}</p>
            <p><strong>Totaalbedrag:</strong> €{{ number_format($order->total_price, 2, ',', '.') }}</p>
            @php
                $orderDate = $order->delivery_date ?? $order->pickup_date;
            @endphp

            <p>
                <strong>{{ $order->type === 'afhalen' ? 'Afhaaldatum' : 'Bezorgdatum' }}:</strong>

                @if($orderDate)
                    {{ \Carbon\Carbon::parse($orderDate)->translatedFormat('l d-m-Y') }}

                    @if(\Carbon\Carbon::parse($orderDate)->isToday())
                        <span class="ml-2 text-xs bg-blue-600 text-white px-2 py-0.5 rounded-full">Vandaag</span>
                    @endif
                @else
                    <span class="text-gray-500">—</span>
                @endif
            </p>

            <p>
                <strong>Betaald:</strong>
                @if($order->paid_at)
                    <span class="text-green-600 font-medium">Ja</span>
                    <span class="text-gray-500 text-xs">({{ \Carbon\Carbon::parse($order->paid_at)->format('d-m-Y H:i') }})</span>
                @else
                    <span class="text-red-600 font-medium">Nee</span>
                @endif
            </p>
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
        <p>Geen bestellingen gevonden.</p>
    </div>
@endforelse
