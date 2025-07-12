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
                    <button @click="tab = 'bezorgen'"
                            :class="tab === 'bezorgen' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'"
                            class="pb-2 px-4 border-b-2 font-medium">
                        Bezorgen
                    </button>
                    <button @click="tab = 'afhalen'"
                            :class="tab === 'afhalen' ? 'border-indigo-500 text-indigo-600' : 'text-gray-500'"
                            class="pb-2 px-4 border-b-2 font-medium">
                        Afhalen
                    </button>
                </div>

                {{-- Bezorgen --}}
                <div x-show="tab === 'bezorgen'" class="space-y-6">
                    @include('admin.orders._order-list', ['orders' => $deliveryOrders])
                </div>

                {{-- Afhalen --}}
                <div x-show="tab === 'afhalen'" class="space-y-6">
                    @include('admin.orders._order-list', ['orders' => $pickupOrders])
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
