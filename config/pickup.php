<?php

return [
    'locations' => [
        'wormerveer' => [
            'name' => 'Wormerveer – Industrieweg 19a',
            'days' => 'Dinsdag t/m zaterdag: 12:00 – 17:00',
            'hours' => [
                'maandag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                'dinsdag' => ['open' => '12:00', 'close' => '17:00'],
                'woensdag' => ['open' => '12:00', 'close' => '17:00'],
                'donderdag' => ['open' => '12:00', 'close' => '17:00'],
                'vrijdag' => ['open' => '12:00', 'close' => '17:00'],
                'zaterdag' => ['open' => '12:00', 'close' => '17:00'],
                'zondag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
            ],
        ],
        'weesp' => [
            'name' => 'Weesp – Eli Café',
            'days' => 'Dinsdag t/m vrijdag: 09:00 – 18:00<br>Zaterdag & zondag: 10:00 – 18:00',
            'hours' => [
                'maandag' => ['open' => '00:00', 'close' => '00:00'], // gesloten
                'dinsdag' => ['open' => '09:00', 'close' => '18:00'],
                'woensdag' => ['open' => '09:00', 'close' => '18:00'],
                'donderdag' => ['open' => '09:00', 'close' => '18:00'],
                'vrijdag' => ['open' => '09:00', 'close' => '18:00'],
                'zaterdag' => ['open' => '10:00', 'close' => '18:00'],
                'zondag' => ['open' => '10:00', 'close' => '18:00'],
            ],
        ],
    ],
    'message' => 'Je kunt je bestelling ook afhalen bij één van onze locaties.',
];
