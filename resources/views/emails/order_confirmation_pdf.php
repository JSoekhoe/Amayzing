<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Orderbevestiging</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background-color: #f7f6f4;
            color: #1f1f1f;
            padding: 2rem;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        h1 {
            color: #386641;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }
        h2 {
            font-size: 1.25rem;
            color: #333;
            margin-top: 2rem;
        }
        ul {
            padding-left: 1rem;
            list-style: none;
        }
        li {
            margin-bottom: 0.5rem;
        }
        .footer {
            font-size: 0.95rem;
            margin-top: 2rem;
        }
        hr {
            margin: 2rem 0;
            border: none;
            border-top: 1px solid #e0e0e0;
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
        @if ($order->ty
