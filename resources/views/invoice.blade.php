<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Factuur #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
    </style>
</head>
<body>
<h1>Factuur #{{ $order->id }}</h1>
<p><strong>Klant:</strong> {{ $order->name }}</p>
<p><strong>Email:</strong> {{ $order->email }}</p>
<p><strong>Telefoon:</strong> {{ $order->phone }}</p>

<table>
    <thead>
    <tr>
        <th>Product</th>
        <th>Aantal</th>
        <th>Prijs per stuk</th>
        <th>Totaal</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>€{{ number_format($item->price, 2) }}</td>
            <td>€{{ number_format($item->price * $item->quantity, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p><strong>Totaal prijs:</strong> €{{ number_format($order->total_price, 2) }}</p>
</body>
</html>
