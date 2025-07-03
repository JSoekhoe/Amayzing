<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8" />
    <title>Orderbevestiging - aMayzing Pastry</title>
    <style>
        /* Basis reset */
        body, p, h1, h2, ul, li {
            margin: 0; padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f6f4;
            color: #1f1f1f;
            padding: 2rem 0;
        }
        .container {
            max-width: 600px;
            background: #fff;
            margin: 0 auto;
            border-radius: 16px;
            padding: 2rem 3rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        h1 {
            font-weight: 700;
            color: #386641;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        h2 {
            font-weight: 600;
            font-size: 1.25rem;
            color: #3a3a3a;
            margin-top: 2.5rem;
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
            font-size: 1rem;
            color: #2e2e2e;
        }
        strong {
            color: #1f1f1f;
        }
        hr {
            border: none;
            border-top: 1px solid #e0e0e0;
            margin: 2rem 0;
        }
        .footer {
            font-size: 0.9rem;
            color: #5a5a5a;
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
            <li><strong>Adres:</strong> {{ $order->address ?? '' }} {{ $order->housenumber }} {{ $order->addition }}</li>
            <li><strong>Postcode:</strong> {{ $order->postcode }}</li>
        @endif
    </ul>

    <h2>Bestelde producten</h2>
    <ul>
        @if ($order->items && $order->items->count() > 0)
            @foreach ($order->items as $item)
                @php
                    $productName = optional($item->product)->name ?? 'Onbekend product';
                    $lineTotal = number_format($item->price * $item->quantity, 2, ',', '.');
                @endphp
                <li>{{ $productName }} × {{ $item->quantity }} — €{{ $lineTotal }}</li>
            @endforeach
        @else
            <li>Geen bestelde producten gevonden.</li>
        @endif
    </ul>

    <p><strong>Totaalprijs:</strong> €{{ number_format($order->total_price, 2, ',', '.') }}</p>

    <hr>

    <p class="footer">
        Heb je nog vragen? Je kunt ons altijd bereiken via e-mail of telefoon.<br><br>
        Met vriendelijke groet,<br>
        <strong>Het aMayzing team</strong>
    </p>
</div>
</body>
</html>
