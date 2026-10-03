<?php
// Function 1: XSS protection. Use this on everything we print.
function e(?string $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

// Function 2: format money
function peso(float $amount): string
{
    return 'PHP ' . number_format($amount, 2);
}

// Function 3: add up the 'total' of every registration
function calculate_total(array $items): float
{
    $sum = 0.0;
    foreach ($items as $item) {
        $sum = $sum + $item['total'];
    }
    return $sum;
}

// Function 4: calculate ticket price times quantity
function calculate_ticket_total(float $price, int $qty): float
{
    return $price * $qty;
}

// Function 5: pass-by-reference (&). It changes the original list.
function add_registration(array &$list, array $record): void
{
    $list[] = $record;
}

// Function 6: static variable. It remembers its value between calls.
function next_ticket_id(int $already_saved): string
{
    static $added = 0;
    $added++;
    return 'TKT-' . str_pad((string) ($already_saved + $added), 3, '0', STR_PAD_LEFT);
}

// Function 7: multi-way branching with if / elseif / else
function get_level(float $total): string
{
    if ($total >= 10000) {
        return 'Platinum';
    } elseif ($total >= 5000) {
        return 'Gold';
    } elseif ($total >= 2000) {
        return 'Silver';
    } else {
        return 'Standard';
    }
}

// Function 8: multi-way branching with match
function group_type(int $qty): string
{
    return match (true) {
        $qty >= 5 => 'Group',
        $qty >= 2 => 'Pair or trio',
        default   => 'Solo',
    };
}

// Function 9: sort by total using the spaceship operator <=>
function sort_by_total(array $list, string $order): array
{
    usort($list, function ($a, $b) use ($order) {
        if ($order === 'asc') {
            return $a['total'] <=> $b['total'];
        }
        return $b['total'] <=> $a['total'];
    });
    return $list;
}