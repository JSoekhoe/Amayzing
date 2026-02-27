<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-serif font-semibold text-gray-800">
            Bestellingen van vandaag
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flashmelding --}}
            @if(session('success'))
                <div class="px-4 py-3 mb-6 bg-green-100 text-green-800 border border-green-200 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-2xl overflow-x-auto border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Ordernr</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Naam</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Bezorgadres</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Telefoon</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Tijdslot</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                    </thead>

                    {{-- ⚠️ GEEN x-data hier --}}
                    <tbody class="divide-y divide-gray-200">

                    @forelse($orders as $order)
                        @php
                            $items = $order->items ?? collect();
                            $totalQty = $items->sum('quantity');
                        @endphp

                        {{-- ✅ eigen tbody per order (stabiel!) --}}
                        <tbody x-data="{ open: false }" class="divide-y divide-gray-200">

                        {{-- Hoofdregel --}}
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 align-top font-medium text-gray-800">
                                <div class="flex items-center gap-3 min-w-0">
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                   border border-gray-200 bg-white text-gray-700 hover:bg-gray-100 transition"
                                        :aria-expanded="open.toString()"
                                    >
                                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg"
                                             class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 5v14m7-7H5"/>
                                        </svg>
                                        <svg x-show="open" x-cloak xmlns="http://www.w3.org/2000/svg"
                                             class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 12H5"/>
                                        </svg>
                                    </button>

                                    <div class="min-w-0">
                                        <div class="truncate">#{{ $order->id }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $items->count() }} regels • {{ $totalQty }} stuks
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 align-top">
                                {{ $order->name }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700 align-top">
                                {{ $order->street }} {{ $order->housenumber }}<br>
                                {{ $order->postcode }}<br>
                                {{ $order->city }}
                            </td>

                            <td class="px-6 py-4 align-top">
                                {{ $order->phone }}
                            </td>

                            <td class="px-6 py-4 align-top text-sm text-gray-700">
                                {{ $order->timeslot ?? 'Nog niet toegewezen' }}
                            </td>

                            <td class="px-6 py-4 align-top">
                                <form method="POST"
                                      action="{{ route('admin.orders.assignTimeslot', $order) }}"
                                      class="flex items-center gap-3">
                                    @csrf

                                    <select name="timeslot"
                                            class="border-gray-300 rounded-lg shadow-sm text-sm
                                                       focus:ring-indigo-500 focus:border-indigo-500">
                                        @foreach($slots as $slot)
                                            <option value="{{ $slot }}"
                                                {{ $order->timeslot === $slot ? 'selected' : '' }}>
                                                {{ $slot }}
                                            </option>
                                        @endforeach
                                    </select>

                                    {{-- ✅ NOOIT WIT --}}
                                    <button
                                        type="submit"
                                        class="appearance-none inline-flex items-center px-4 py-2
                                                   text-sm font-medium text-white bg-indigo-600 rounded-lg
                                                   hover:bg-indigo-700 focus:outline-none focus:ring-2
                                                   focus:ring-indigo-500 focus:ring-offset-1 transition">
                                        Opslaan
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Uitklapbare producten --}}
                        <tr x-show="open" x-cloak>
                            <td colspan="6" class="px-6 pb-6">
                                <div class="mt-3 rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-sm font-semibold text-gray-800">
                                            Producten in bestelling
                                        </p>

                                        <a href="{{ route('admin.orders.show', $order) }}"
                                           class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                            Details bekijken
                                        </a>
                                    </div>

                                    @if($items->isEmpty())
                                        <p class="text-sm text-gray-500">Geen producten gevonden.</p>
                                    @else
                                        <ul class="text-sm text-gray-700 space-y-2">
                                            @foreach($items as $item)
                                                <li class="flex justify-between gap-4">
                                                        <span class="truncate">
                                                            {{ $item->product?->name ?? 'Product verwijderd' }}
                                                        </span>
                                                    <span class="whitespace-nowrap text-gray-600">
                                                            x{{ $item->quantity }}
                                                        </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        </tbody>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-center text-gray-500">
                                Geen bestellingen vandaag
                            </td>
                        </tr>
                        @endforelse

                        </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
