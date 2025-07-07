    <?php

    return [
        'locations' => [
            'wormerveer' => [
                'name' => 'Wormerveer – Industrieweg 19A',
                'days' => 'Dinsdag t/m zondag: 12:00 – 17:00',
                'hours' => [
                    'maandag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                    'dinsdag' => ['open' => '12:00', 'close' => '17:00'],
                    'woensdag' => ['open' => '12:00', 'close' => '17:00'],
                    'donderdag' => ['open' => '12:00', 'close' => '17:00'],
                    'vrijdag' => ['open' => '12:00', 'close' => '17:00'],
                    'zaterdag' => ['open' => '12:00', 'close' => '17:00'],
                    'zondag' => ['open' => '00:00', 'close' => '23:00'], // gesloten
                ],
            ],
        ],
        'message' => 'Je kunt je bestelling ook afhalen bij:',
    ];
