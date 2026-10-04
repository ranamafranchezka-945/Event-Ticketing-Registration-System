<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GATEPASS - Event Ticketing</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header>
    <h1>GATEPASS</h1>
    <p class="header-counter"><?= e($summary) ?></p>
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
            <?php foreach ($events as $event_highlight): ?>
            <div class="hero-event">
                <span class="hero-event-icon" aria-hidden="true">&#10022;</span>
                <span>
                    <strong><?= e($event_highlight['name']) ?></strong>
                    <small><?= e($event_highlight['date']) ?></small>
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
            <p><?= e($receipt['event_name'] ?? ($events[$receipt['event']]['name'] ?? $receipt['event'])) ?></p>
            <p><?= e($receipt['event_date'] ?? ($events[$receipt['event']]['date'] ?? '')) ?></p>
            <?php if (!empty($receipt['event_guest'])): ?>
            <p>Guest: <?= e($receipt['event_guest']) ?></p>
            <?php endif; ?>
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
        <div class="section-head">
            <h2 class="section-title">Register</h2>
            <p class="section-sub">Fill in your details, pick a date and tier, and your ticket is ready in seconds.</p>
        </div>

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

            <div class="form-row">
                <label>Full name
                    <input type="text" name="name" value="<?= e($name) ?>" autocomplete="name">
                </label>

                <label>Email
                    <input type="text" name="email" value="<?= e($email) ?>" autocomplete="email">
                </label>
            </div>

            <div class="form-row">
                <label>Age
                    <input type="text" name="age" value="<?= e($age) ?>" inputmode="numeric">
                </label>

                <label>Number of tickets
                    <input type="text" name="qty" value="<?= e($qty) ?>" inputmode="numeric">
                    <span class="field-hint">1 to 10 tickets per registration</span>
                </label>
            </div>

            <!-- Event dropdown -->
            <label>Event
                <select name="event">
                    <option value="">Choose an event</option>
                    <?php foreach ($events as $key => $ev): ?>
                    <option value="<?= e($key) ?>" <?= (($_POST['event'] ?? '') === $key) ? 'selected' : '' ?>>
                        <?= e($ev['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div class="event-date-groups">
                <?php foreach ($events as $event_key => $event_info): ?>
                <fieldset class="event-date-group" data-event-date-group="<?= e($event_key) ?>" <?php if ($event !== $event_key): ?>hidden<?php endif; ?>>
                    <legend>Choose an event date</legend>
                    <?php foreach ($event_info['schedule'] as $scheduled_day): ?>
                    <label class="event-date-option">
                        <input type="radio" name="event_date" value="<?= e($scheduled_day['date']) ?>" <?= $event === $event_key && $event_date === $scheduled_day['date'] ? 'checked' : '' ?>>
                        <span>
                            <?= e($scheduled_day['day']) ?> - <?= e($scheduled_day['date']) ?>
                            <?php if (!empty($scheduled_day['guest'])): ?>
                            <small>Guest: <?= e($scheduled_day['guest']) ?></small>
                            <?php endif; ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </fieldset>
                <?php endforeach; ?>
            </div>

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

            <div class="badge-upload">
                <label for="badge-photo">Badge photo</label>
                <span class="field-hint" id="badge-hint">JPG, PNG or WebP, max 2 MB</span>
                <input id="badge-photo" aria-describedby="badge-hint" type="file" name="badge_photo" accept=".jpg,.jpeg,.png,.webp">
                <?php if (is_array($temp_badge) && isset($temp_badge['filename'], $temp_badge['original_name']) && basename($temp_badge['filename']) === $temp_badge['filename'] && is_file('uploads/temp_badges/' . $temp_badge['filename'])): ?>
                <input type="hidden" name="temp_badge" value="<?= e($temp_badge['filename']) ?>">
                <p class="badge-attached"><span aria-hidden="true">&#10003;</span> Photo already attached: <strong><?= e($temp_badge['original_name']) ?></strong></p>
                <?php endif; ?>
            </div>

            <div class="form-submit">
                <label class="agree">
                    <input type="checkbox" name="agree" value="yes" <?= $agree === 'yes' ? 'checked' : '' ?>>
                    I accept the event terms.
                </label>

                <button type="submit" class="btn-primary">Get my ticket</button>
            </div>
        </form>
    </section>


    <!-- ATTENDEE LIST -->
    <section class="box">
        <div class="attendees-heading">
            <h2 class="attendees-title">
                Attendees
                <span class="attendee-count"><?= $attendee_count ?></span>
            </h2>

            <nav class="sort-controls" aria-label="Sort attendees">
                <a class="sort-pill <?= $sort === 'desc' ? 'bg-pink-600 text-white border-pink-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-pink-50' ?>" href="?sort=desc" <?= $sort === 'desc' ? 'aria-current="true"' : '' ?>>Highest total</a>
                <a class="sort-pill <?= $sort === 'asc' ? 'bg-pink-600 text-white border-pink-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-pink-50' ?>" href="?sort=asc" <?= $sort === 'asc' ? 'aria-current="true"' : '' ?>>Lowest total</a>
            </nav>
        </div>

        <?php if (empty($people)): ?>
            <p>No one has registered yet.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="attendee-table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Event</th>
                            <th scope="col">Date</th>
                            <th scope="col">Tier</th>
                            <th scope="col">Ticket code</th>
                            <th scope="col" class="num">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($people as $number => $person): ?>
                        <tr>
                            <td><?= $number + 1 ?></td>
                            <td>
                                <span class="who">
                                    <?php if (!empty($person['photo'])): ?>
                                    <img src="uploads/<?= e($person['photo']) ?>" alt="Badge photo of <?= e($person['name']) ?>">
                                    <?php endif; ?>
                                    <span><?= e($person['name']) ?></span>
                                </span>
                            </td>
                            <td>
                                <?= e($person['event_name'] ?? ($events[$person['event']]['name'] ?? $person['event'])) ?>
                                <?php if (!empty($person['event_guest'])): ?><small>Guest: <?= e($person['event_guest']) ?></small><?php endif; ?>
                            </td>
                            <td><?= e($person['event_date'] ?? ($events[$person['event']]['date'] ?? '')) ?></td>
                            <td><?= e($tiers[$person['tier']]['label']) ?> &times; <?= (int) $person['qty'] ?></td>
                            <td><span class="code"><?= e($person['id']) ?></span></td>
                            <td class="num">
                                <b><?= e(peso($person['total'])) ?></b>
                                <small class="attendee-status"><?= e(get_level($person['total'])) ?></small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

</main>
<script>
const eventSelect = document.querySelector('select[name="event"]');
const eventDateGroups = document.querySelectorAll('[data-event-date-group]');
const sortControls = document.querySelector('.sort-controls');
const attendeeTableBody = document.querySelector('.attendee-table tbody');

eventSelect.addEventListener('change', () => {
    eventDateGroups.forEach((group) => {
        const isSelectedEvent = group.dataset.eventDateGroup === eventSelect.value;
        group.hidden = !isSelectedEvent;

        if (!isSelectedEvent) {
            group.querySelectorAll('input[name="event_date"]').forEach((radio) => {
                radio.checked = false;
            });
        }
    });
});

sortControls.addEventListener('click', async (event) => {
    const link = event.target.closest('a[href]');
    if (!link || !sortControls.contains(link)) {
        return;
    }

    event.preventDefault();

    try {
        const response = await fetch(link.href);
        if (!response.ok) {
            throw new Error('Could not sort attendees.');
        }

        const responseDocument = new DOMParser().parseFromString(await response.text(), 'text/html');
        const updatedTableBody = responseDocument.querySelector('.attendee-table tbody');
        if (attendeeTableBody && updatedTableBody) {
            attendeeTableBody.replaceChildren(
                ...Array.from(updatedTableBody.childNodes, (node) => document.importNode(node, true))
            );
        } else if (attendeeTableBody || updatedTableBody) {
            throw new Error('The attendee table response was incomplete.');
        }

        const selectedSort = new URL(link.href).searchParams.get('sort');
        sortControls.querySelectorAll('a[href]').forEach((sortLink) => {
            const sort = new URL(sortLink.href).searchParams.get('sort');
            const responseLink = Array.from(responseDocument.querySelectorAll('.sort-controls a[href]'))
                .find((candidate) => new URL(candidate.href).searchParams.get('sort') === sort);

            if (sort === selectedSort && responseLink) {
                sortLink.className = responseLink.className;
                sortLink.setAttribute('aria-current', 'true');
            } else {
                sortLink.removeAttribute('aria-current');
                if (responseLink) {
                    sortLink.className = responseLink.className;
                }
            }
        });
    } catch (error) {
        window.location.assign(link.href);
    }
});
</script>
</body>
</html>