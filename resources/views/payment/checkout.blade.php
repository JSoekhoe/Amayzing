<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Betaling</h1>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-white border border-gray-200 rounded-3xl shadow-md p-10 text-center">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Bevestig je betaling</h2>

            <p class="text-lg text-gray-600 mb-6">
                Te betalen bedrag:
                <span class="text-2xl font-bold text-gray-900">€{{ number_format($amount, 2, ',', '.') }}</span>
            </p>

            <button id="pay-button"
                    class="w-full bg-gray-800 hover:bg-gray-700 text-white font-semibold py-3 rounded-full shadow-md transition duration-200 focus:outline-none focus:ring-4 focus:ring-gray-300">
                Betaal nu
            </button>

            <div id="error-message" class="text-red-600 mt-4 text-sm min-h-[1.5rem] font-medium"></div>
        </div>
    </div>

    <script>
        document.getElementById('pay-button').addEventListener('click', async () => {
            const errorEl = document.getElementById('error-message');
            errorEl.textContent = ''; // reset foutmelding

            try {
                const response = await fetch("{{ route('payment.process') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        amount: "{{ number_format($amount, 2, '.', '') }}",
                        order_id: {{ $order->id }},
                    }),
                });

                if (!response.ok) {
                    const text = await response.text();
                    errorEl.textContent = 'Fout bij betaling: ' + text;
                    console.error('Response niet OK:', text);
                    return;
                }

                const data = await response.json();

                if (data.error) {
                    errorEl.textContent = data.error;
                    return;
                }

                if (data.checkoutUrl) {
                    window.location.href = data.checkoutUrl;
                } else {
                    errorEl.textContent = "Onbekende fout: geen checkoutUrl ontvangen.";
                }
            } catch (error) {
                errorEl.textContent = "Er is iets misgegaan met het starten van de betaling.";
                console.error('Fetch error:', error);
            }
        });
    </script>
</x-app-layout>
