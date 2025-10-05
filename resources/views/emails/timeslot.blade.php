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

<p>
    Voor vragen of opmerkingen kunt u een bericht sturen naar
    <strong>
        <a href="tel:+31644042554" style="color:#000; text-decoration:none;">+31 6 44 04 25 54</a>
    </strong>.
</p>

<p>Met vriendelijke groet,<br>
    Het aMayzing team</p>
</body>
</html>
