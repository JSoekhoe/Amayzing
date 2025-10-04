<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-serif font-semibold text-gray-800">
            Bestellingen van vandaag
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="px-4 py-3 mb-4 bg-green-100 text-green-800 border border-green-200 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Ordernr</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Naam</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Telefoon</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Tijdslot</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4">{{ $order->id }}</td>
                            <td class="px-6 py-4">{{ $order->name }}</td>
                            <td class="px-6 py-4">{{ $order->phone }}</td>
                            <td class="px-6 py-4">
                                {{ $order->timeslot ?? 'Nog niet toegewezen' }}
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.orders.assignTimeslot', $order) }}">
                                    @csrf
                                    <select name="timeslot" class="border-gray-300 rounded-lg shadow-sm">
                                        @foreach($slots as $slot)
                                            <option value="{{ $slot }}" {{ $order->timeslot === $slot ? 'selected' : '' }}>
                                                {{ $slot }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-8 py-3 rounded-full shadow-md transition">
                                    Opslaan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Geen bestellingen vandaag</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
test
