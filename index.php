<?php
// Show PHP errors while developing (remove when done)
ini_set('display_errors', '1');
error_reporting(E_ALL);

session_start();

// Visit index.php?reset=1 to clear all saved registrations (handy for demos)
if (isset($_GET['reset'])) {
    $_SESSION = [];
    header('Location: index.php');
    exit;
}

require 'includes/data.php';
require 'includes/functions.php';

// Make the uploads folder if it is missing
if (!is_dir('uploads')) {
    mkdir('uploads');
}

// Our saved registrations live in the session
if (!isset($_SESSION['registrations'])) {
    $_SESSION['registrations'] = [];
}

$legacy_event_details = [
    'techfest' => ['name' => 'Bicol TechFest', 'date' => 'Nov 14, 2026'],
    'sound'    => ['name' => 'Kabsat Sound Fest', 'date' => 'Dec 05, 2026'],
    'artwalk'  => ['name' => 'Art and Food Walk', 'date' => 'Dec 19, 2026'],
];

foreach ($_SESSION['registrations'] as &$registration) {
    if (!isset($registration['event_name']) && isset($legacy_event_details[$registration['event']])) {
        $registration['event_name'] = $legacy_event_details[$registration['event']]['name'];
    }
    if (!isset($registration['event_date']) && isset($legacy_event_details[$registration['event']])) {
        $registration['event_date'] = $legacy_event_details[$registration['event']]['date'];
    }

    $registration['total'] = calculate_ticket_total(
        (float) $tiers[$registration['tier']]['price'],
        (int) $registration['qty']
    );
    unset($registration['subtotal'], $registration['discount_rate'], $registration['discount'], $registration['fee'], $registration['tax']);
}
unset($registration);

// Sticky form values (?? gives an empty default when nothing was sent)
$name      = $_POST['name'] ?? '';
$email     = $_POST['email'] ?? '';
$age       = $_POST['age'] ?? '';
$event     = $_POST['event'] ?? '';
$tier      = $_POST['tier'] ?? '';
$qty       = $_POST['qty'] ?? '1';
$agree     = $_POST['agree'] ?? '';
$event_date = $_POST['event_date'] ?? '';
$temp_badge = $_SESSION['temp_badge'] ?? null;
$selected_schedule = null;

$errors = [];

// ---------- Form was submitted ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = trim($name);
    $email = trim($email);
    $age   = trim($age);
    $qty   = trim($qty);

    // Name
    if (empty($name)) {
        $errors[] = 'Please enter your name.';
    } elseif (strlen($name) < MIN_NAME_LENGTH) {
        $errors[] = 'Name must be at least ' . MIN_NAME_LENGTH . ' characters.';
    }

    // Email
    if (empty($email)) {
        $errors[] = 'Please enter your email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'That email is not valid.';
    }

    // Age
    if (empty($age)) {
        $errors[] = 'Please enter your age.';
    } elseif (!filter_var($age, FILTER_VALIDATE_INT, ['options' => ['min_range' => 13, 'max_range' => 120]])) {
        $errors[] = 'Age must be a number from 13 to 120.';
    }

    // Event and tier must be one of our choices
    if (!array_key_exists($event, $events)) {
        $errors[] = 'Please choose an event.';
    }
    if (empty($_POST['event_date'])) {
        $errors[] = 'Please choose an event date.';
    } elseif (array_key_exists($event, $events)) {
        foreach ($events[$event]['schedule'] as $scheduled_day) {
            if ($event_date === $scheduled_day['date']) {
                $selected_schedule = $scheduled_day;
                break;
            }
        }

        if ($selected_schedule === null) {
            $errors[] = 'Please choose a valid date for the selected event.';
        }
    } else {
        $errors[] = 'Please choose a valid date for the selected event.';
    }
    if (!array_key_exists($tier, $tiers)) {
        $errors[] = 'Please choose a ticket tier.';
    }

    // Quantity
    if (!filter_var($qty, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 10]])) {
        $errors[] = 'Tickets must be a number from 1 to 10.';
    }

    // ---------- File upload checks ----------
    $badge_upload = $_FILES['badge_photo'] ?? null;
    $badge_temp_directory = 'uploads/temp_badges';
    $badge_extension = '';
    $badge_source = '';
    $badge_is_new_upload = false;
    $posted_temp_badge = $_POST['temp_badge'] ?? '';

    if ($badge_upload !== null && !empty($badge_upload['name'])) {
        if ($badge_upload['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'The upload failed. Try a smaller photo.';
        } elseif ($badge_upload['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Photo is too big. Maximum is 2 MB.';
        } else {
            $badge_extension = strtolower(pathinfo($badge_upload['name'], PATHINFO_EXTENSION));

            if (!in_array($badge_extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $errors[] = 'Photo must be a JPG, PNG or WebP image.';
            } else {
                $badge_source = $badge_upload['tmp_name'];
                $badge_is_new_upload = true;
            }
        }
    } elseif (
        is_array($temp_badge)
        && isset($temp_badge['filename'], $temp_badge['original_name'], $temp_badge['extension'])
        && $posted_temp_badge === $temp_badge['filename']
        && basename($temp_badge['filename']) === $temp_badge['filename']
        && in_array($temp_badge['extension'], ['jpg', 'jpeg', 'png', 'webp'], true)
        && is_file($badge_temp_directory . '/' . $temp_badge['filename'])
    ) {
        $badge_extension = $temp_badge['extension'];
        $badge_source = $badge_temp_directory . '/' . $temp_badge['filename'];
    } else {
        $errors[] = 'Please upload a badge photo.';
    }

    if ($badge_is_new_upload && !empty($errors)) {
        if (!is_dir($badge_temp_directory) && !mkdir($badge_temp_directory, 0700, true) && !is_dir($badge_temp_directory)) {
            $errors[] = 'The badge photo could not be saved temporarily. Please try again.';
        } else {
            $temporary_filename = uniqid('badge_') . '.' . $badge_extension;
            $temporary_path = $badge_temp_directory . '/' . $temporary_filename;

            if (move_uploaded_file($badge_upload['tmp_name'], $temporary_path)) {
                if (is_array($temp_badge) && isset($temp_badge['filename'])) {
                    $old_temporary_path = $badge_temp_directory . '/' . basename($temp_badge['filename']);
                    if (is_file($old_temporary_path)) {
                        unlink($old_temporary_path);
                    }
                }

                $temp_badge = [
                    'filename' => $temporary_filename,
                    'original_name' => basename($badge_upload['name']),
                    'extension' => $badge_extension,
                ];
                $_SESSION['temp_badge'] = $temp_badge;
                $badge_source = $temporary_path;
            } else {
                $errors[] = 'The badge photo could not be saved temporarily. Please try again.';
            }
        }
    }

    // Terms are last in the form, so report this validation last.
    if ($agree !== 'yes') {
        $errors[] = 'Please accept the terms.';
    }

    // ---------- No errors: save everything ----------
    if (empty($errors)) {

        // Give the file a safe unique name, then move it to uploads/.
        $new_file_name = uniqid('badge_') . '.' . $badge_extension;
        $destination = 'uploads/' . $new_file_name;
        $photo_saved = $badge_is_new_upload
            ? move_uploaded_file($badge_source, $destination)
            : rename($badge_source, $destination);

        if ($photo_saved) {
            if (is_array($temp_badge) && isset($temp_badge['filename'])) {
                $old_temporary_path = $badge_temp_directory . '/' . basename($temp_badge['filename']);
                if (is_file($old_temporary_path)) {
                    unlink($old_temporary_path);
                }
            }
            unset($_SESSION['temp_badge']);

            // build the record and save it
            $record = [
                'id'        => next_ticket_id(count($_SESSION['registrations'])),
                'name'      => $name,
                'email'     => $email,
                'age'       => (int) $age,
                'event'     => $event,
                'event_name' => $events[$event]['name'],
                'event_date' => $event_date,
                'event_guest' => $selected_schedule['guest'],
                'tier'      => $tier,
                'qty'       => (int) $qty,
                'photo'     => $new_file_name,
                'total'     => calculate_ticket_total((float) $tiers[$tier]['price'], (int) $qty),
            ];
            add_registration($_SESSION['registrations'], $record);
            $_SESSION['receipt'] = $record;

            // Post/Redirect/Get: stops the form being sent twice on refresh
            header('Location: index.php');
            exit;
        }

        $errors[] = 'The badge photo could not be saved. Please try again.';
    }
}

// ---------- Prepare data for the page ----------
$receipt = $_SESSION['receipt'] ?? null;
if ($receipt !== null) {
    $receipt['total'] = calculate_ticket_total(
        (float) $tiers[$receipt['tier']]['price'],
        (int) $receipt['qty']
    );
}
unset($_SESSION['receipt']);   // show the receipt only once

$sort = $_GET['sort'] ?? 'desc';
if ($sort !== 'asc' && $sort !== 'desc') {
    $sort = 'desc';
}
$attendees = $_SESSION['registrations'];
$people = sort_by_total($attendees, $sort);

$attendee_count = count($attendees);
$total_revenue = calculate_total($attendees);
$summary = $attendee_count . ' Registered | PHP ' . number_format($total_revenue, 2) . ' collected';

// asort: tiers ordered from cheapest to most expensive
$prices = [];
foreach ($tiers as $key => $t) {
    $prices[$key] = $t['price'];
}
asort($prices);

require 'templates/page.php';