<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bevestiging bestelling - Amayzing</title>
</head>
<body>
<h2>Bedankt voor je bestelling, {{ $order->name }}!</h2>

<p>We hebben je bestelling ontvangen en gaan deze zo snel mogelijk verwerken.</p>

<h3>Bestelgegevens:</h3>
<ul>
    <li><strong>Naam:</strong> {{ $order->name }}</li>
    <li><strong>Email:</strong> {{ $order->email }}</li>
    <li><strong>Telefoon:</strong> {{ $order->phone }}</li>
    <li><strong>Type bestelling:</strong> {{ ucfirst($order->type) }}</li>
    @if($order->type === 'bezorgen')
        <li><strong>Adres:</strong> {{ $order->address }}</li>
        <li><strong>Postcode:</strong> {{ $order->postcode }}</li>
    @elseif($order->type === 'afhalen')
        <li><strong>Afhaaltijd:</strong> {{ \Carbon\Carbon::parse($order->pickup_time)->format('H:i') }}</li>
    @endif
</ul>

<h3>Bestelde producten:</h3>
<ul>
    @foreach($order->items as $item)
        <li>
            {{ $item->product->name }} x {{ $item->quantity }} — &euro;{{ number_format($item->price * $item->quantity, 2) }}
        </li>
    @endforeach
</ul>

<p><strong>Totaalprijs:</strong> &euro;{{ number_format($order->total_price, 2) }}</p>

<p>Heb je nog vragen? Je kunt ons bereiken via telefoon of e-mail.</p>

<p>Met vriendelijke groet,<br>Het Amayzing team</p>
</body>
</html>
