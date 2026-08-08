@php
    use Illuminate\Support\Str;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h1 class="font-serif text-3xl font-semibold text-gray-900 leading-tight mb-12 tracking-tight">
            Veelgestelde vragen
        </h1>
    </x-slot>

    <section class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-24 py-12 bg-gray-50 rounded-2xl shadow-lg">

        <div class="flex justify-end mb-10">
            <a href="{{ route('admin.faqs.create') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-8 py-3 rounded-full shadow-md transition">
                Nieuwe FAQ toevoegen
            </a>
        </div>

        @if(session('success'))
            <div class="mb-8 p-5 bg-gray-100 border border-gray-300 text-gray-700 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto rounded-xl shadow-xl bg-white">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-8 py-5 text-left text-sm font-serif font-semibold text-gray-700 uppercase tracking-wider">
                        Vraag
                    </th>

                    <th class="px-8 py-5 text-left text-sm font-serif font-semibold text-gray-700 uppercase tracking-wider">
                        Antwoord
                    </th>

                    <th class="px-8 py-5 text-left text-sm font-serif font-semibold text-gray-700 uppercase tracking-wider">
                        Volgorde
                    </th>

                    <th class="px-8 py-5 text-left text-sm font-serif font-semibold text-gray-700 uppercase tracking-wider">
                        Status
                    </th>

                    <th class="px-8 py-5 text-left text-sm font-serif font-semibold text-gray-700 uppercase tracking-wider">
                        Acties
                    </th>
                </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-300">

                @forelse($faqs as $faq)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- Vraag --}}
                        <td class="px-8 py-5 align-top">
                            <div class="font-medium text-gray-900">
                                {{ $faq->question }}
                            </div>
                        </td>

                        {{-- Antwoord --}}
                        <td class="px-8 py-5 align-top">
                            <div class="text-gray-700 max-w-xl">
                                {{ Str::limit($faq->answer, 150) }}
                            </div>
                        </td>

                        {{-- Volgorde --}}
                        <td class="px-8 py-5 align-top whitespace-nowrap text-gray-700">
                            {{ $faq->sort_order }}
                        </td>

                        {{-- Status --}}
                        <td class="px-8 py-5 align-top whitespace-nowrap">
                            @if($faq->is_active)
                                <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                    Actief
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-500">
                                    Inactief
                                </span>
                            @endif
                        </td>

                        {{-- Acties --}}
                        <td class="px-8 py-5 align-top whitespace-nowrap space-x-4">

                            <a href="{{ route('admin.faqs.edit', $faq) }}"
                               class="text-yellow-600 hover:text-yellow-900 font-semibold transition">
                                Bewerken
                            </a>

                            <form action="{{ route('admin.faqs.destroy', $faq) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Weet je zeker dat je deze FAQ wilt verwijderen?');">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-red-600 hover:text-red-900 font-semibold transition">
                                    Verwijderen
                                </button>

                            </form>

                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="5"
                            class="px-8 py-8 text-center text-gray-400 italic">
                            Geen FAQ's gevonden.
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>

    </section>
</x-app-layout>
