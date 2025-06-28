<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#f7f6f4] px-6 py-16">
        <div class="bg-white rounded-3xl shadow-lg max-w-md w-full p-10">
            <h1 class="text-3xl font-serif font-bold text-[#386641] mb-8 text-center">
                Betaling
            </h1>

            <form id="payment-form" class="space-y-6">
                <div id="card-element" class="p-4 border border-gray-300 rounded-md"></div>

                <button id="submit" type="submit"
                        class="w-full bg-[#9bd5cb] hover:bg-[#78b9aa] text-[#1a433d] font-semibold py-3 rounded-full shadow transition">
                    Betaal €<span id="amount-display"></span>
                </button>

                <div id="error-message" class="text-red-600 mt-3 text-center text-sm min-h-[1.25rem]"></div>
            </form>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe("{{ env('STRIPE_KEY') }}");
        const elements = stripe.elements();
        const cardElement = elements.create('card', {
            style: {
                base: {
                    color: '#1a433d',
                    fontFamily: '"Georgia", serif',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#9bd5cb'
                    }
                },
                invalid: {
                    color: '#dc2626',
                }
            }
        });
        cardElement.mount('#card-element');

        const amount = {{ session('payment_amount', 0) }};
        document.getElementById('amount-display').textContent = amount.toFixed(2);

        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const response = await fetch("{{ route('payment.process') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({amount})
            });

            const data = await response.json();

            if(data.error){
                document.getElementById('error-message').textContent = data.error;
                return;
            }

            const result = await stripe.confirmCardPayment(data.clientSecret, {
                payment_method: {card: cardElement}
            });

            if (result.error) {
                document.getElementById('error-message').textContent = result.error.message;
            } else if (result.paymentIntent.status === 'succeeded') {
                window.location.href = "{{ route('thankyou') }}";
            }
        });
    </script>
</x-app-layout>
