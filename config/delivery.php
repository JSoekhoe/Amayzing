<?php

return [
    'max_distance_km' => 10,
    'last_order_time' => '22:00',
    'weekday_start_time' => '13:00',
    'delivery_end_time' => '20:30',

    'cities' => [
        'arnhem' => [
            'center' => ['lat' => 51.9851, 'lng' => 5.8987],
            'delivery_day' => 'wednesday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'almere' => [
            'center' => ['lat' => 52.37527, 'lng' => 5.21933],
            'delivery_day' => 'thursday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'utrecht' => [
            'center' => ['lat' => 52.0907, 'lng' => 5.1214],
            'delivery_day' => 'friday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'denhaag' => [
            'center' => ['lat' => 52.0705, 'lng' => 4.3007],
            'delivery_day' => 'saturday',
            'delivery_time' => 'vanaf 11:00 uur',
        ],
        'rotterdam' => [
            'center' => ['lat' => 51.9244, 'lng' => 4.4777],
            'delivery_day' => 'sunday',
            'delivery_time' => 'vanaf 11:00 uur',
        ],
        'eindhoven' => [
            'center' => ['lat' => 51.4416, 'lng' => 5.4697],
            'delivery_day' => 'wednesday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'groningen' => [
            'center' => ['lat' => 53.2194, 'lng' => 6.5665],
            'delivery_day' => 'wednesday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'amsterdam' => [
            'center' => ['lat' => 52.3676, 'lng' => 4.9041],
            'delivery_day' => 'thursday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'tilburg' => [
            'center' => ['lat' => 51.5606, 'lng' => 5.0919],
            'delivery_day' => 'wednesday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'antwerpen' => [
            'center' => ['lat' => 51.2194, 'lng' => 4.4025],
            'delivery_day' => 'thursday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'denbosch' => [
            'center' => ['lat' => 51.6978, 'lng' => 5.3037],
            'delivery_day' => 'wednesday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'enschede' => [
            'center' => ['lat' => 52.2215, 'lng' => 6.8937],
            'delivery_day' => 'thursday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'apeldoorn' => [
            'center' => ['lat' => 52.2112, 'lng' => 5.9699],
            'delivery_day' => 'friday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'leeuwarden' => [
            'center' => ['lat' => 53.2012, 'lng' => 5.7999],
            'delivery_day' => 'wednesday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'maastricht' => [
            'center' => ['lat' => 50.8514, 'lng' => 5.6900],
            'delivery_day' => 'wednesday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'breda' => [
            'center' => ['lat' => 51.5719, 'lng' => 4.7682],
            'delivery_day' => 'thursday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'venlo' => [
            'center' => ['lat' => 51.3704, 'lng' => 6.1724],
            'delivery_day' => 'wednesday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'alkmaar' => [
            'center' => ['lat' => 52.6324, 'lng' => 4.7534],
            'delivery_day' => 'thursday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
        'bergenopzoom' => [
            'center' => ['lat' => 51.4950, 'lng' => 4.2871],
            'delivery_day' => 'friday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
    ],

    'fixed_schedule' => [
        'saturday' => 'denhaag',
        'sunday' => 'rotterdam',
    ],

    'delivery_schedule' => [
        49 => [
            'wednesday' => 'almere',
            'thursday'  => 'denbosch',
            'friday' => 'arnhem',
        ],
        50 => [
            'wednesday' => 'tilburg',
            'thursday'  => 'groningen',
            'friday' => 'amsterdam',
        ],
        51 => [
            'wednesday' => 'enschede',
            'thursday'  => 'apeldoorn',
            'friday' => 'amersfoort',
        ],
        52 => [
            'wednesday' => 'breda',
            'thursday'  => 'bergenopzoom',
            'friday' => 'rotterdam',
        ],
    ],

    'pickup' => [
        'address' => 'Centrum van',
        'opening_hours' => 'maandag t/m zaterdag, 12:00 - 17:00 uur',
        'message' => '🛍️ Plan je afhaalmoment zelf in binnen onze openingstijden',
    ],
];
