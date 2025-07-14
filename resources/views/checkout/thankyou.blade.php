<x-app-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-[#f2f2f2] px-6 py-16">
        <div class="max-w-md bg-white rounded-3xl shadow-lg p-10 text-center border border-gray-200">
            <h1 class="text-5xl font-serif font-bold text-gray-800 mb-6 leading-tight">
                Bedankt voor je bestelling!
            </h1>
            <p class="text-lg text-gray-600 mb-10">
                We hebben je bestelling #{{ $order->id }} ontvangen en nemen snel contact met je op.
            </p>
            <a href="{{ route('home') }}"
               class="inline-block bg-gray-200 text-gray-800 font-semibold px-10 py-3 rounded-full shadow-md hover:bg-gray-300 transition">
                Terug naar home
            </a>
        </div>
    </div>
</x-app-layout>
