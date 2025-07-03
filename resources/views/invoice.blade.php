<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Factuur #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f6f4;
            color: #333;
            margin: 0;
            padding: 40px;
        }

        .invoice-container {
            background-color: #fff;
            border-radius: 24px;
            padding: 40px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        h1 {
            font-family: 'Georgia', serif;
            font-size: 32px;
            text-align: center;
            color: #3a3a3a;
            margin-bottom: 30px;
        }

        .client-info p {
            margin: 6px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            border-radius: 12px;
            overflow: hidden;
        }

        th {
            background-color: #eae6df;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            color: #444;
        }

        td {
            padding: 12px;
            border-top: 1px solid #eee;
            font-size: 14px;
        }

        tfoot td {
            font-weight: bold;
            font-size: 15px;
            border-top: 2px solid #ddd;
        }

        .total {
            text-align: right;
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <h1>Factuur #{{ $order->id }}</h1>

    <div class="client-info">
        <p><strong>Naam:</strong> {{ $order->name }}</p>
        <p><strong>Email:</strong> {{ $order->email }}</p>
        <p><strong>Telefoon:</strong> {{ $order->phone }}</p>
    </div>

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

    <p class="total">Totaal prijs: €{{ number_format($order->total_price, 2) }}</p>
</div>
</body>
</html>
