    <?php

    return [
        'locations' => [
            'wormerveer' => [
                'name' => 'Wormerveer – Industrieweg 19A',
                'street' => 'Industrieweg',
                'housenumber' => '19',
                'addition' => 'A',
                'postcode' => '1521 NA',
                'city' => 'Wormerveer',
                'days' => 'Dinsdag t/m Zaterdag: 10:00 – 15:00',
                'hours' => [
                    'maandag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                    'dinsdag' => ['open' => '11:00', 'close' => '15:00'],
                    'woensdag' => ['open' => '11:00', 'close' => '15:00'],
                    'donderdag' => ['open' => '11:00', 'close' => '15:00'],
                    'vrijdag' => ['open' => '11:00', 'close' => '15:00'],
                    'zaterdag' => ['open' => '11:00', 'close' => '15:00'],
                    'zondag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                ],
            ],
            'marina' => [
                'name' => 'De Marina Amsterdam – Krijn Taconiskade 430',
                'street' => 'Krijn Taconiskade',
                'housenumber' => '430',
                'addition' => null,
                'postcode' => '1078 HW',
                'city' => 'Amsterdam',
                'days' => 'Woensdag t/m Zondag: 15:00 – 23:00',
                'footnote' => 'Bestellen bij deze locatie is niet mogelijk;
                 je kunt er echter wel langskomen om ter plekke onze desserts te kopen.',
                'hours' => [
                    'maandag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                    'dinsdag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                    'woensdag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                    'donderdag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                    'vrijdag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                    'zaterdag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                    'zondag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                ],
            ],
        ],
        'message' => 'Je kunt je bestelling ook afhalen bij:',
    ];
