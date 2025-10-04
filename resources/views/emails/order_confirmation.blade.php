<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8" />
    <title>Orderbevestiging - aMayzing Pastry</title>
    <style>
        body, p, h1, h2, ul, li {
            margin: 0; padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f5;
            color: #333333;
            padding: 2rem 0;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 0 auto;
            border-radius: 12px;
            padding: 2rem 3rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #dddddd;
        }
        h1 {
            font-weight: 700;
            color: #4a4a4a;
            font-size: 1.8rem;
            margin-bottom: 1.25rem;
        }
        h2 {
            font-weight: 600;
            font-size: 1.3rem;
            color: #5e5e5e;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
        }
        p {
            line-height: 1.6;
            margin-bottom: 1rem;
            color: #555555;
        }
        ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 1rem;
        }
        li {
            margin-bottom: 0.7rem;
            font-size: 1rem;
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
            border-bottom: 1px solid transparent;
            transition: border-color 0.3s ease;
        }
        a:hover {
            border-bottom: 1px solid #999999;
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

        @php $totaal = 0; @endphp
        @if ($order->type === 'bezorgen' && $deliveryInfo && $deliveryInfo->allowed)
            <h2>Bezorginformatie</h2>
            @if ($deliveryInfo)
                <p><strong>Adres voor bezorging:</strong></p>
                <p>{!! $deliveryInfo->adresVolledig !!}</p>
            @endif
        <p><strong>Bezorgtijd:</strong> tussen <strong>{{ $deliveryInfo->nearestCityCenter['delivery_time'] ?? 'onbekend' }}</strong> en <strong>{{ config('delivery.delivery_end_time') }}</strong> uur.</p>
        <p>Op de dag van bezorging ontvang je een Mail met het gepland tijdslot van de bezorger. Zo weet je precies wanneer je bestelling aankomt.</p>
    @endif

    <p><strong>Totaal incl. bezorgkosten:</strong> €{{ number_format($order->total_price, 2, ',', '.') }}</p>

    <hr>

    <p class="footer">
        Heb je nog vragen? Je kunt ons altijd bereiken via e-mail of telefoon.<br><br>
        Met vriendelijke groet,<br>
        <strong>Het aMayzing team</strong>
    </p>
</div>
</body>
</html>
