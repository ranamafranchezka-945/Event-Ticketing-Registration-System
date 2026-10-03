<?php
// ---------- SETTINGS (easy to change) ----------
const MIN_NAME_LENGTH = 4;                // change 4 to 8 for the live challenge

// ---------- DATA (multidimensional associative arrays) ----------
$events = [
    'chezka_comeback' => [
        'name' => '3C: Chezka’s Comeback Concert',
        'date' => 'November 4, 2026',
        'schedule' => [
            ['day' => 'Day 1', 'date' => 'November 4, 2026', 'guest' => 'Pia Jane Lastrollo'],
            ['day' => 'Day 2', 'date' => 'November 6, 2026', 'guest' => 'Angel Charm Rabino'],
        ],
    ],
    'techfest_after_dark' => [
        'name' => 'Bicol TechFest After Dark',
        'date' => 'November 14, 2026',
        'schedule' => [
            ['day' => 'Day 1', 'date' => 'November 14, 2026', 'guest' => ''],
            ['day' => 'Day 2', 'date' => 'November 15, 2026', 'guest' => ''],
        ],
    ],
    'midnight_music_fest' => [
        'name' => 'Midnight Music Fest',
        'date' => 'December 5, 2026',
        'schedule' => [
            ['day' => 'Day 1', 'date' => 'December 5, 2026', 'guest' => ''],
            ['day' => 'Day 2', 'date' => 'December 7, 2026', 'guest' => ''],
        ],
    ],
    'cyberglow_esports_night' => [
        'name' => 'CyberGlow E-Sports Night',
        'date' => 'January 24, 2027',
        'schedule' => [
            ['day' => 'Day 1', 'date' => 'January 24, 2027', 'guest' => ''],
            ['day' => 'Day 2', 'date' => 'January 26, 2027', 'guest' => ''],
        ],
    ],
];

$tiers = [
    'general'   => ['label' => 'General',   'price' => 1500, 'perks' => 'Standing area, wristband'],
    'vip'       => ['label' => 'VIP',       'price' => 4500, 'perks' => 'Reserved seat, free drink'],
    'backstage' => ['label' => 'Backstage', 'price' => 9000, 'perks' => 'Meet the speakers, front row'],
];
