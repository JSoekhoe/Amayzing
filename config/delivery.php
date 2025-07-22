<?php

return [
    'cities' => [
        'arnhem' => [
            'center' => ['lat' => 51.9851, 'lng' => 5.8987],
            'delivery_day' => 'wednesday',
            'delivery_time' => 'van 13:00 tot 20:30 uur',
        ],
//        'breda' => [
//            'center' => ['lat' => 51.5719, 'lng' => 4.7683],
//            'delivery_day' => 'thursday',
//            'delivery_time' => 'vanaf 14:00 uur',
//        ],
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

    ],

    'max_distance_km' => 10,

    'last_order_time' => '22:00',
    'weekday_start_time' => '13:00',
    'weekend_start_time' => '11:00',
    'delivery_end_time' => '20:30',

    'pickup' => [
        'address' => 'Centrum van',
        'opening_hours' => 'maandag t/m zaterdag, 12:00 - 17:00 uur',
        'message' => '🛍️ Plan je afhaalmoment zelf in binnen onze openingstijden',
    ],
];
