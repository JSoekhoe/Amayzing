<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-serif font-semibold text-gray-800">
            Overzicht bestellingen
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flashmelding --}}
            @if(session('success'))
                <div class="px-4 py-3 mb-4 bg-green-100 text-green-800 border border-green-200 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tabs --}}
            <div x-data="{ tab: 'bezorgen' }" class="space-y-6">
                <div class="flex space-x-4 border-b border-gray-200 mb-4">
                    <button
                        type="button"
                        @click="tab = 'bezorgen'"
                        :class="tab === 'bezorgen' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500 border-transparent'"
                        class="pb-2 px-4 border-b-2 font-medium">
                        Bezorgen
                    </button>

                    <button
                        type="button"
                        @click="tab = 'afhalen'"
                        :class="tab === 'afhalen' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500 border-transparent'"
                        class="pb-2 px-4 border-b-2 font-medium">
                        Afhalen
                    </button>
                </div>

                {{-- Filter dropdown alleen tonen als er bestellingen zijn --}}
                @if($pickupOrders->count() || $deliveryOrders->count())
                    <div class="mb-6">
                        <form method="GET" action="{{ route('admin.orders.index') }}">
                            <label for="week" class="mr-2 font-medium text-gray-700">Week:</label>
                            <select
                                name="week"
                                id="week"
                                onchange="this.form.submit()"
                                class="border-gray-300 rounded-lg shadow-sm"
                            >
                                <option value="">Alle weken</option>
                                @foreach($weeks as $week)
                                    <option value="{{ $week['key'] }}" {{ $selectedWeek == $week['key'] ? 'selected' : '' }}>
                                        {{ $week['label'] }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- handig: reset knop --}}
                            @if(!empty($selectedWeek))
                                <a href="{{ route('admin.orders.index') }}"
                                   class="ml-3 text-sm text-gray-600 underline hover:text-gray-900">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>
                @endif

                {{-- Bezorgen --}}
                <div x-show="tab === 'bezorgen'" x-cloak class="space-y-6">
                    @include('admin.orders._order-list', ['orders' => $deliveryOrders])

                    {{-- Pagination bezorgen --}}
                    <div class="mt-6">
                        {{ $deliveryOrders->links() }}
                    </div>
                </div>

                {{-- Afhalen --}}
                <div x-show="tab === 'afhalen'" x-cloak class="space-y-6">
                    @include('admin.orders._order-list', ['orders' => $pickupOrders])

                    {{-- Pagination afhalen --}}
                    <div class="mt-6">
                        {{ $pickupOrders->links() }}
                    </div>
                </div>
            </div>

            {{-- Producten overzicht totaal --}}
            @if(isset($salesByType))
                {{-- BEZORGEN --}}
                @if($salesByType['bezorgen']->count())
                    <div class="mt-12">
                        <h3 class="text-2xl font-semibold mb-4">Totaal verkochte producten (Bezorgen)</h3>
                        <div class="overflow-x-auto bg-white shadow rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Product</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Aantal</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                @foreach($salesByType['bezorgen'] as $productName => $quantity)
                                    <tr>
                                        <td class="px-6 py-3">{{ $productName }}</td>
                                        <td class="px-6 py-3">{{ $quantity }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- AFHALEN --}}
                @if($salesByType['afhalen']->count())
                    <div class="mt-12">
                        <h3 class="text-2xl font-semibold mb-4">Totaal verkochte producten (Afhalen)</h3>
                        <div class="overflow-x-auto bg-white shadow rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Product</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Aantal</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                @foreach($salesByType['afhalen'] as $productName => $quantity)
                                    <tr>
                                        <td class="px-6 py-3">{{ $productName }}</td>
                                        <td class="px-6 py-3">{{ $quantity }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
