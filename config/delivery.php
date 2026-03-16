<?php

return [
    'max_distance_km' => 10,
    'last_order_time' => '22:00',
    'weekday_start_time' => '13:00',
    'delivery_end_time' => '20:30',

    'cities' => [
        'alkmaar' => [
            'center' => ['lat' => 52.6324, 'lng' => 4.7534],
        ],
        'almere' => [
            'center' => ['lat' => 52.37527, 'lng' => 5.21933],
        ],
        'amersfoort' => [
            'center' => ['lat' => 52.1561113, 'lng' => 5.3878266],
        ],
        'amsterdam' => [
            'center' => ['lat' => 52.3676, 'lng' => 4.9041],
        ],
        'antwerpen' => [
            'center' => ['lat' => 51.2194, 'lng' => 4.4025],
        ],
        'apeldoorn' => [
            'center' => ['lat' => 52.2112, 'lng' => 5.9699],
        ],
        'arnhem' => [
            'center' => ['lat' => 51.9851, 'lng' => 5.8987],
        ],
        'bergenopzoom' => [
            'center' => ['lat' => 51.4950, 'lng' => 4.2871],
        ],
        'breda' => [
            'center' => ['lat' => 51.5719, 'lng' => 4.7682],
        ],
        'culemborg' => [
            'center' => ['lat' => 51.9555674, 'lng' => 5.2271806],
        ],
        'denbosch' => [
            'center' => ['lat' => 51.6978, 'lng' => 5.3037],
        ],
        'denhaag' => [
            'center' => ['lat' => 52.0705, 'lng' => 4.3007],
        ],
        'deventer' => [
            'center' => ['lat' => 52.2550, 'lng' => 6.1639],
        ],
        'eindhoven' => [
            'center' => ['lat' => 51.4416, 'lng' => 5.4697],
        ],
        'enschede' => [
            'center' => ['lat' => 52.2215, 'lng' => 6.8937],
        ],
        'groningen' => [
            'center' => ['lat' => 53.2194, 'lng' => 6.5665],
        ],
        'hilversum' => [
            'center' => ['lat' => 52.22333, 'lng' => 5.17639],
        ],
        'leeuwarden' => [
            'center' => ['lat' => 53.2012, 'lng' => 5.7999],
        ],
        'leiden' => [
            'center' => ['lat' => 52.160114, 'lng' => 4.497010],
        ],
        'lelystad' => [
            'center' => ['lat' => 52.5185, 'lng' => 5.4714],
        ],
        'maastricht' => [
            'center' => ['lat' => 50.8514, 'lng' => 5.6900],
        ],
        'rotterdam' => [
            'center' => ['lat' => 51.9244, 'lng' => 4.4777],
        ],
        'tilburg' => [
            'center' => ['lat' => 51.5606, 'lng' => 5.0919],
        ],
        'utrecht' => [
            'center' => ['lat' => 52.0907, 'lng' => 5.1214],
        ],
        'venlo' => [
            'center' => ['lat' => 51.3704, 'lng' => 6.1724],
        ],
    ],

    // datum-gedreven bezorgplanning
    'date_schedule' => [
        '2026-03-18' => 'denbosch',
        '2026-03-19' => 'enschede',
        '2026-03-20' => 'amersfoort',
        '2026-03-21' => 'denhaag',
        '2026-03-22' => 'rotterdam',

        '2026-03-25' => 'groningen',
        '2026-03-26' => 'utrecht',
        '2026-03-27' => 'tilburg',
        '2026-03-28' => 'arnhem',
        '2026-03-29' => 'rotterdam',

        '2026-04-01' => 'apeldoorn',
        '2026-04-02' => 'eindhoven',
        '2026-04-03' => 'lelystad',
        '2026-04-04' => 'denhaag',
        '2026-04-05' => 'rotterdam',

        '2026-04-08' => 'hilversum',
        '2026-04-09' => 'deventer',
        '2026-04-10' => 'culemborg',
        '2026-04-11' => 'breda',
        '2026-04-12' => 'rotterdam',
    ],
];
