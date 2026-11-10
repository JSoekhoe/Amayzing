<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welkom bij Jamay Tuller Patisserie</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f0e9; font-family:Arial, sans-serif; color:#30251f;">

<div style="max-width:600px; margin:35px auto; background:#ffffff;">

    <div style="padding:45px 30px 35px; text-align:center;">

        <p style="font-size:11px; letter-spacing:4px; text-transform:uppercase; color:#a18464;">
            Jamay Tuller Patisserie
        </p>

        <h1 style="font-size:32px; font-weight:normal; line-height:1.3; margin:25px 0 15px;">
            Welkom bij een wereld vol
            <span style="color:#a18464; font-style:italic;">zoete verrassingen.</span>
        </h1>

        <p style="font-size:15px; line-height:1.8; color:#75665b;">
            Beste {{ $subscriber->name }},
        </p>

        <p style="font-size:15px; line-height:1.8; color:#75665b;">
            Wat leuk dat je je hebt ingeschreven voor onze nieuwsbrief!
            Vanaf nu houden we je graag op de hoogte van alles wat er
            te ontdekken valt bij Jamay Tuller Patisserie.
        </p>

        {{-- GIVEAWAY BEVESTIGING --}}
        <div style="background:#f9f6f1; border:1px solid #e8ddcf; padding:25px 20px; margin:30px 0;">

            <p style="font-size:10px; letter-spacing:3px; text-transform:uppercase; color:#a18464; margin:0 0 12px;">
                Een extra zoete kans
            </p>

            <h2 style="font-size:23px; font-weight:normal; color:#30251f; margin:0;">
                Je doet automatisch mee!
            </h2>

            <p style="font-size:14px; line-height:1.8; color:#75665b; margin:15px 0 0;">
                Met je nieuwsbriefinschrijving maak je automatisch kans op
                een jaar lang genieten van onze desserts.
            </p>

            <p style="font-size:32px; font-weight:normal; color:#a18464; margin:20px 0 5px;">
                € 600
            </p>

            <p style="font-size:12px; line-height:1.7; color:#75665b; margin:0;">
                12 maanden lang iedere maand een doosje desserts
                ter waarde van € 50.
            </p>

            <p style="font-size:11px; color:#a39487; margin:15px 0 0;">
                Eén winnaar wordt geselecteerd.
            </p>

        </div>

        <div style="height:1px; background:#e5dbd0; margin:30px 0;"></div>

        {{-- NIEUWSBRIEF --}}
        <h2 style="font-size:22px; font-weight:normal; margin-bottom:15px;">
            Wat kun je verwachten?
        </h2>

        <p style="font-size:14px; line-height:1.9; color:#75665b;">
            Via onze nieuwsbrief delen we regelmatig:
        </p>

        <div style="text-align:left; background:#f4f0e9; padding:25px; margin:25px 0;">

            <p style="font-size:14px; line-height:1.8; color:#75665b; margin-top:0;">
                <strong style="color:#30251f;">Nieuwe creaties</strong><br>
                Ontdek onze nieuwste patisserie en bijzondere smaken.
            </p>

            <p style="font-size:14px; line-height:1.8; color:#75665b;">
                <strong style="color:#30251f;">Seizoensspecials</strong><br>
                Blijf op de hoogte van feestelijke boxen en tijdelijke collecties.
            </p>

            <p style="font-size:14px; line-height:1.8; color:#75665b; margin-bottom:0;">
                <strong style="color:#30251f;">Exclusieve aanbiedingen</strong><br>
                Ontvang als eerste nieuws over acties en speciale aanbiedingen.
            </p>

        </div>

        <p style="font-size:14px; line-height:1.9; color:#75665b;">
            We sturen je alleen nieuws wanneer er iets moois te delen is.
            Zo blijft iedere nieuwsbrief een kleine verrassing in je inbox.
        </p>

        <p style="font-size:15px; line-height:1.8; color:#30251f; margin-top:30px;">
            Veel leesplezier en hopelijk tot snel!<br><br>
            <strong>Jamay Tuller</strong><br>
            <span style="color:#a18464;">Jamay Tuller Patisserie</span>
        </p>

        {{-- WEBSITE BUTTON --}}
        <div style="margin-top:35px;">
            <a href="https://www.jamaytuller.com"
               style="display:inline-block; background:#30251f; color:#ffffff; text-decoration:none; padding:15px 28px; font-size:12px; letter-spacing:2px; text-transform:uppercase;">
                Ontdek onze website
            </a>
        </div>

    </div>

    {{-- FOOTER --}}
    <div style="background:#f4f0e9; padding:25px 30px; text-align:center;">

        <p style="font-size:12px; color:#88776a; line-height:1.7;">
            Jamay Tuller Patisserie<br>
            Industrieweg 19, Wormerveer<br>
            WhatsApp: +31 6 44042554
        </p>

        <p style="font-size:11px; color:#a39487; line-height:1.7;">
            Je ontvangt deze e-mail omdat je je hebt aangemeld voor onze nieuwsbrief.
            <br>
            <a href="{{ route('newsletter.unsubscribe', $subscriber->unsubscribe_token) }}"
               style="color:#88776a;">
                Uitschrijven voor de nieuwsbrief
            </a>
        </p>

    </div>

</div>

</body>
</html>
