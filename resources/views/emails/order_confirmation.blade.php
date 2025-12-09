<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Orderbevestiging - aMayzing Pastry</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f5;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            width: 100%;
            padding: 2rem 0;
            background-color: #f4f4f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 2rem 3rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #dddddd;
        }
        h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #4a4a4a;
            margin-bottom: 1rem;
        }
        h2 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #5e5e5e;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        p {
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 1rem;
        }
        li {
            margin-bottom: 0.7rem;
            color: #444444;
        }
        strong {
            color: #222222;
        }
        hr {
            border: none;
            border-top: 1px solid #e2e2e2;
            margin: 2rem 0;
        }
        .footer {
            font-size: 0.9rem;
            color: #777777;
            line-height: 1.4;
            text-align: center;
            margin-top: 3rem;
            border-top: 1px solid #e2e2e2;
            padding-top: 1.5rem;
            font-style: italic;
        }
        a {
            color: #555555;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="container">
        <h1>Bedankt voor je bestelling, {{ $order->name }}!</h1>
        <p>We hebben je bestelling ontvangen en gaan deze zo snel mogelijk verwerken.</p>

        <h2>Bestelgegevens</h2>
        <ul>
            <li><strong>Naam:</strong> {{ $order->name }}</li>
            <li><strong>Email:</strong> {{ $order->email }}</li>
            <li><strong>Telefoon:</strong> {{ $order->phone }}</li>
            <li><strong>Type bestelling:</strong> {{ ucfirst($order->type) }}</li>
            <li><strong>Datum:</strong> {{ $order->created_at->format('d-m-Y H:i') }}</li>
        </ul>

        <h2>Bestelde producten</h2>
        <ul>
            @php $totaal = 0; @endphp
            @foreach ($order->items as $item)
                @php
                    $product = $item->product;
                    $prijs = $product ? $product->price : 0;
                    $subtotaal = $prijs * $item->quantity;
                    $totaal += $subtotaal;
                @endphp
                <li>{{ $item->quantity }} × {{ $product->name ?? 'Product verwijderd' }} – €{{ number_format($subtotaal, 2, ',', '.') }}</li>
            @endforeach
        </ul>

        @if ($order->type === 'afhalen')
            @php $pickup = $order->pickup_location_data; @endphp
            <h2>Afhaalinformatie</h2>
            <ul>
                <li><strong>Adres:</strong> {{ $order->street }} {{ $order->housenumber }}{{ $order->addition ? ' '.$order->addition : '' }}</li>
                <li><strong>Postcode & Plaats:</strong> {{ $order->postcode }} {{ $order->city }}</li>
                <li><strong>Afhaaldatum:</strong> {{ \Carbon\Carbon::parse($order->pickup_date)->format('d-m-Y') }}</li>
                <li><strong>Afhaaltijd:</strong> {{ $order->pickup_time }} uur</li>
            </ul>
        @elseif ($order->type === 'bezorgen')
            <h2>Bezorginformatie</h2>
            <ul>
                <li><strong>Adres:</strong> {{ $order->street }} {{ $order->housenumber }}{{ $order->addition ? ' '.$order->addition : '' }}</li>
                <li><strong>Postcode & Plaats:</strong> {{ $order->postcode }} {{ $order->city }}</li>
                <li><strong>Bezorgdatum:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y') }}</li>
                <li><strong>Bezorgtijd:</strong> tussen 13:00 en 20:30 uur</li>
                <li>U ontvangt op de bezorgdag een mail met een specifiek tijdslot.</li>
            </ul>
        @endif

        <hr>
        <p><strong>Totaal incl. bezorgkosten:</strong> €{{ number_format($order->total_price, 2, ',', '.') }}</p>

        <div class="footer">
            Heb je vragen? Mail ons via <a href="mailto:Amayzingpastry@gmail.com">Amayzingpastry@gmail.com</a>.<br>
            Met vriendelijke groet,<br>
            <strong>aMayzing Pastry</strong>
        </div>
    </div>
</div>
</body>
</html>
