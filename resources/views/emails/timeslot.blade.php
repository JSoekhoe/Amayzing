<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bezorgtijd aMayzing</title>
</head>
<body>
<p>Beste {{ $order->name }},</p>

<p>Uw bestelling wordt vandaag geleverd tussen <strong>{{ $timeslot }}</strong>.</p>

<p>We kijken ernaar uit om uw bestelling te bezorgen!</p>

<p>Met vriendelijke groet,<br>
    Het aMayzing team</p>
</body>
</html>
