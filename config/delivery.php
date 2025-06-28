<?php
return [
    'cities' => [
        'arnhem' => [
            'center' => ['lat' => 51.9851, 'lng' => 5.8987],
            'delivery_day' => 'wednesday',
            'delivery_time' => 'vanaf 14:00 uur',
        ],
        'groningen' => [
            'center' => ['lat' => 53.2194, 'lng' => 6.5665],
            'delivery_day' => 'thursday',
            'delivery_time' => 'vanaf 14:00 uur',
        ],
        'utrecht' => [
            'center' => ['lat' => 52.0907, 'lng' => 5.1214],
            'delivery_day' => 'friday',
            'delivery_time' => 'vanaf 14:00 uur',
        ],
        'breda' => [
            'center' => ['lat' => 51.5719, 'lng' => 4.7683],
            'delivery_day' => 'saturday',
            'delivery_time' => 'vanaf 11:00 uur',
        ],
        'rotterdam' => [
            'center' => ['lat' => 51.9244, 'lng' => 4.4777],
            'delivery_day' => 'sunday',
            'delivery_time' => 'vanaf 11:00 uur',
        ],
    ],

    'max_distance_km' => 10,

    'last_order_time' => '22:00',
    'delivery_end_time' => '21:30',

    'pickup' => [
        'address' => 'Centrum van',
        'opening_hours' => 'maandag t/m zaterdag, 10:00 - 18:00 uur',
        'message' => '🛍️ Plan je afhaalmoment zelf in binnen onze openingstijden',
    ],
];
