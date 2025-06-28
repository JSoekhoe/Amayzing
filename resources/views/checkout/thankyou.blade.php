<x-app-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-[#f7f6f4] px-6 py-16">
        <div class="max-w-md bg-white rounded-3xl shadow-lg p-10 text-center">
            <h1 class="text-5xl font-serif font-bold text-[#386641] mb-6 leading-tight">
                Bedankt voor je bestelling!
            </h1>
            <p class="text-lg text-[#6a6a6a] mb-10">
                We hebben je bestelling ontvangen en nemen snel contact met je op.
            </p>
            <a href="{{ route('home') }}"
               class="inline-block bg-[#9bd5cb] text-[#1a433d] font-semibold px-10 py-3 rounded-full shadow-md hover:bg-[#78b9aa] transition">
                Terug naar home
            </a>
        </div>
    </div>
</x-app-layout>
