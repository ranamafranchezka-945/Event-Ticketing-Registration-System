<?php
// ---------- SETTINGS (easy to change) ----------
const MIN_NAME_LENGTH = 4;                // change 4 to 8 for the live challenge

// ---------- DATA (multidimensional associative arrays) ----------
$events = [
    'techfest' => ['name' => 'Bicol TechFest',    'date' => 'Nov 14, 2026', 'venue' => 'Naga Convention Hall'],
    'sound'    => ['name' => 'Kabsat Sound Fest', 'date' => 'Dec 05, 2026', 'venue' => 'Penafrancia Grounds'],
    'artwalk'  => ['name' => 'Art and Food Walk', 'date' => 'Dec 19, 2026', 'venue' => 'Magsaysay Avenue'],
];

$tiers = [
    'general'   => ['label' => 'General',   'price' => 1500, 'perks' => 'Standing area, wristband'],
    'vip'       => ['label' => 'VIP',       'price' => 4500, 'perks' => 'Reserved seat, free drink'],
    'backstage' => ['label' => 'Backstage', 'price' => 9000, 'perks' => 'Meet the speakers, front row'],
];
