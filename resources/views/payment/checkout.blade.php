@extends('layouts.app')

@section('content')
    <h1>Betaling</h1>

    <form id="payment-form">
        <div id="card-element"><!-- Stripe Element komt hier --></div>
        <button id="submit">Betaal €<span id="amount-display"></span></button>
        <div id="error-message"></div>
    </form>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe("{{ env('STRIPE_KEY') }}");
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        const amount = {{ session('payment_amount', 0) }}; // Zorg dat je dit in sessie zet in controller bij checkout

        document.getElementById('amount-display').textContent = amount.toFixed(2);

        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const response = await fetch("{{ route('payment.process') }}", {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
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
@endsection
