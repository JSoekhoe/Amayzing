    <?php

    return [
        'locations' => [
            'wormerveer' => [
                'name' => 'Wormerveer – Industrieweg 19A',
                'days' => 'Dinsdag t/m Zaterdag: 10:00 – 15:00',
                'hours' => [
                    'maandag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                    'dinsdag' => ['open' => '10:00', 'close' => '15:00'],
                    'woensdag' => ['open' => '10:00', 'close' => '15:00'],
                    'donderdag' => ['open' => '10:00', 'close' => '15:00'],
                    'vrijdag' => ['open' => '10:00', 'close' => '15:00'],
                    'zaterdag' => ['open' => '10:00', 'close' => '15:00'],
                    'zondag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                ],
            ],
        ],
        'message' => 'Je kunt je bestelling ook afhalen bij:',
    ];
