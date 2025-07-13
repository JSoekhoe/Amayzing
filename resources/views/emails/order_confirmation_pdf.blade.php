<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Orderbevestiging</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background-color: #f4f4f5;
            color: #333333;
            padding: 2rem;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
            border: 1px solid #dddddd;
        }
        h1 {
            color: #555555;
            font-size: 1.75rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        h2 {
            font-size: 1.25rem;
            color: #666666;
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        ul {
            padding-left: 1rem;
            list-style: none;
            margin-bottom: 1rem;
        }
        li {
            margin-bottom: 0.5rem;
            color: #444444;
            font-size: 1rem;
        }
        strong {
            color: #222222;
            font-weight: 600;
        }
        hr {
            margin: 2rem 0;
            border: none;
            border-top: 1px solid #e1e1e1;
        }
        .footer {
            font-size: 0.95rem;
            margin-top: 2rem;
            color: #777777;
            line-height: 1.4;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Bedankt voor je bestelling, {{ $order->name }}!</h1>
    <p>We hebben je bestelling ontvangen en gaan deze zo snel mogelijk verwerken.</p>

    <h2>Bestelgegevens</h2>
    <ul>
        <li><strong>Naam:</strong> {{ $order->name }}</li>
        <li><strong>Email:</strong> {{ $order->email }}</li>
        <li><strong>Telefoon:</strong> {{ $order->phone }}</li>
        <li><strong>Type bestelling:</strong> {{ ucfirst($order->type) }}</li>
        @if ($order->type === 'bezorgen')
            <li><strong>Adres:</strong>
                {{ $order->straat }}
                {{ $order->housenumber }}
                {{ $order->addition }}
                , {{ $order->postcode }}
            </li>
        @endif
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
            <li>
                {{ $item->quantity }} × {{ $product->name ?? 'Product verwijderd' }} –
                €{{ number_format($subtotaal, 2, ',', '.') }}
            </li>
        @endforeach
    </ul>
    <hr>
    <p><strong>Totaal:</strong> €{{ number_format($totaal, 2, ',', '.') }}</p>

    <div class="footer">
        <p>Heb je vragen? Neem contact met ons op via Amayzingpastry@gmail.com.</p>
    </div>
</div>
</body>
</html>
