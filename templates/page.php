<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gatepass - Event Ticketing</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <h1>Gatepass</h1>
    <p><?= e($summary) ?></p>
</header>

<main>

    <section class="hero" aria-labelledby="hero-title">
        <div class="hero-copy">
            <p class="hero-eyebrow">GATEPASS / BICOL EVENTS</p>
            <h2 id="hero-title">Make room for a night worth remembering.</h2>
            <p>Find your people, discover something new, and get your next great night out on the calendar.</p>
            <a class="hero-cta" href="#register">Register Now</a>
        </div>

        <div class="hero-highlights" role="group" aria-label="Upcoming event highlights">
            <p class="hero-highlights-title">Coming up in Bicol</p>
            <?php foreach ($events as $event): ?>
            <div class="hero-event">
                <span class="hero-event-icon" aria-hidden="true">&#10022;</span>
                <span>
                    <strong><?= e($event['name']) ?></strong>
                    <small><?= e($event['date']) ?></small>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- RECEIPT: shows once after a successful registration -->
    <?php if ($receipt !== null): ?>
    <section class="receipt">
        <img src="uploads/<?= e($receipt['photo']) ?>" alt="Badge photo">

        <div>
            <p class="code"><?= e($receipt['id']) ?></p>
            <h2><?= e($receipt['name']) ?></h2>
            <p><?= e($events[$receipt['event']]['name']) ?></p>
            <p><?= e($events[$receipt['event']]['date']) ?></p>
            <p><?= e(get_level($receipt['total'])) ?> attendee | <?= e(group_type($receipt['qty'])) ?></p>
        </div>

        <table>
            <tr class="grand">
                <td>Total</td>
                <td><?= e(peso($receipt['total'])) ?></td>
            </tr>
        </table>
    </section>
    <?php endif; ?>


    <!-- REGISTRATION FORM -->
    <section class="box" id="register">
        <h2>Register</h2>

        <!-- Error messages -->
        <?php if (!empty($errors)): ?>
        <div class="errors">
            <strong>To proceed, please fill these out:</strong>
            <ul>
                <?php foreach ($errors as $message): ?>
                <li><?= e($message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="post" action="index.php" enctype="multipart/form-data">

            <label>Full name
                <input type="text" name="name" value="<?= e($name) ?>">
            </label>

            <label>Email
                <input type="text" name="email" value="<?= e($email) ?>">
            </label>

            <label>Age
                <input type="text" name="age" value="<?= e($age) ?>">
            </label>

            <!-- Event dropdown -->
            <label>Event
                <select name="event">
                    <option value="">Choose an event</option>
                    <?php foreach ($events as $key => $ev): ?>
                    <option value="<?= e($key) ?>" <?= (($_POST['event'] ?? '') === $key) ? 'selected' : '' ?>>
                        <?= e($ev['name']) ?> (<?= e($ev['date']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <!-- Ticket tiers (cheapest first) -->
            <p class="label">Ticket tier</p>
            <div class="tiers">
                <?php foreach ($prices as $key => $price): ?>
                <label class="tier">
                    <input type="radio" name="tier" value="<?= e($key) ?>" <?= $tier === $key ? 'checked' : '' ?>>
                    <span>
                        <b><?= e($tiers[$key]['label']) ?></b> - <?= e(peso($price)) ?><br>
                        <small><?= e($tiers[$key]['perks']) ?></small>
                    </span>
                </label>
                <?php endforeach; ?>
            </div>

            <label>Number of tickets
                <input type="text" name="qty" value="<?= e($qty) ?>">
            </label>

            <div class="badge-upload">
                <label for="badge-photo">Badge photo (JPG, PNG or WebP, max 2 MB)</label>
                <input id="badge-photo" type="file" name="badge_photo" accept=".jpg,.jpeg,.png,.webp">
                <?php if (is_array($temp_badge) && isset($temp_badge['filename'], $temp_badge['original_name']) && basename($temp_badge['filename']) === $temp_badge['filename'] && is_file('uploads/temp_badges/' . $temp_badge['filename'])): ?>
                <input type="hidden" name="temp_badge" value="<?= e($temp_badge['filename']) ?>">
                <p class="badge-attached"><span aria-hidden="true">&#10003;</span> Photo already attached: <strong><?= e($temp_badge['original_name']) ?></strong></p>
                <?php endif; ?>
            </div>

            <label class="agree">
                <input type="checkbox" name="agree" value="yes" <?= $agree === 'yes' ? 'checked' : '' ?>>
                I accept the event terms.
            </label>

            <button type="submit">Get my ticket</button>
        </form>
    </section>


    <!-- ATTENDEE LIST -->
    <section class="box">
        <div class="attendees-heading">
            <h2 class="attendees-title">
                Attendees
                <span class="attendee-count"><?= $total_people ?></span>
            </h2>

            <nav class="sort-controls" aria-label="Sort attendees">
                <a class="sort-pill <?= $sort === 'desc' ? 'bg-pink-600 text-white border-pink-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-pink-50' ?>" href="?sort=desc" <?= $sort === 'desc' ? 'aria-current="true"' : '' ?>>Highest total</a>
                <a class="sort-pill <?= $sort === 'asc' ? 'bg-pink-600 text-white border-pink-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-pink-50' ?>" href="?sort=asc" <?= $sort === 'asc' ? 'aria-current="true"' : '' ?>>Lowest total</a>
            </nav>
        </div>

        <?php if (empty($people)): ?>
            <p>No one has registered yet.</p>
        <?php else: ?>
            <?php foreach ($people as $number => $person): ?>
            <div class="person">
                <?php if (!empty($person['photo'])): ?>
                <img src="uploads/<?= e($person['photo']) ?>" alt="Badge photo">
                <?php endif; ?>

                <div>
                    <b><?= $number + 1 ?>. <?= e($person['name']) ?></b><br>
                    <small>
                        <?= e($events[$person['event']]['name']) ?> |
                        <?= e($tiers[$person['tier']]['label']) ?> x <?= $person['qty'] ?> |
                        <?= e($person['id']) ?>
                    </small><br>
                </div>

                <div class="money">
                    <b><?= e(peso($person['total'])) ?></b><br>
                    <small><?= e(get_level($person['total'])) ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</main>
</body>
</html>