<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#f7f6f4] px-6 py-16">
        <div class="bg-white rounded-3xl shadow-lg max-w-md w-full p-10 text-center">
            <h1 class="text-3xl font-serif font-bold text-[#386641] mb-8">Betaling</h1>

            <p>Te betalen bedrag: <strong>€{{ number_format($amount, 2) }}</strong></p>

            <button id="pay-button" class="w-full bg-[#9bd5cb] hover:bg-[#78b9aa] text-[#1a433d] font-semibold py-3 rounded-full shadow transition mt-6">
                Betaal nu
            </button>

            <div id="error-message" class="text-red-600 mt-3 text-center text-sm min-h-[1.25rem]"></div>
        </div>
    </div>

    <script>
        document.getElementById('pay-button').addEventListener('click', async () => {
            try {
                const response = await fetch("{{ route('payment.process') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        amount: {{ json_encode($amount) }},
                        order_id: {{ $order->id }},
                    }),
                });

                const data = await response.json();

                if (data.error) {
                    document.getElementById('error-message').textContent = data.error;
                    return;
                }

                if (data.checkoutUrl) {
                    window.location.href = data.checkoutUrl; // redirect naar Mollie checkout
                }
            } catch (error) {
                document.getElementById('error-message').textContent = "Er is iets misgegaan met het starten van de betaling.";
                console.error(error);
            }
        });
    </script>
</x-app-layout>
