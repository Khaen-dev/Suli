<?php

require "required/config.php";
require "required/favo.php";

$userId = getLoggedInUserId();

if (isset($_POST['plan_id'])) {
    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
    $hasSubscription = queryOne($conn, "SELECT id FROM user_subscriptions WHERE userid = ?", [$userId]);

    if ($hasSubscription === null) {
        runQuery($conn, "INSERT INTO user_subscriptions (userid, subscription, expires) VALUES (?, ?, ?)", [$userId, $_POST['plan_id'], $expires]);
    } else {
        runQuery($conn, "UPDATE user_subscriptions SET subscription = ?, expires = ? WHERE userid = ?", [$_POST['plan_id'], $expires, $userId]);
    }

    header("Location: mystores.php");
    exit();
}

$subscription = getSubscription($conn, $userId);
$plans = queryAll($conn, "SELECT * FROM pricing ORDER BY tier");
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Árazás</title>
    <link rel="stylesheet" href="css/boti.css">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>
    <?php require "required/nav.php"; ?>

    <?php if ($subscription['has_subscription']) { ?>
        <p class="plan-line center">
            Jelenlegi csomagod: <strong><?= $subscription['name'] ?></strong>, lejár: <?= $subscription['expires'] ?>
        </p>
    <?php } ?>

    <div class="pricing-container">
        <?php foreach ($plans as $plan) { ?>
            <form method="post" class="pricing-card <?= $plan['tier'] == 2 ? 'featured' : '' ?>">
                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">

                <p class="plan-name"><?= $plan['name'] ?></p>
                <p class="plan-price"><?= number_format($plan['price'], 0, '', ' ') ?> Ft <span class="plan-period">/ hó</span></p>

                <ul class="plan-features">
                    <?php if ($plan['store_count'] === null) { ?>
                        <li>✓ <strong>Korlátlan bolt</strong></li>
                    <?php } else { ?>
                        <li>✓ Maximum <strong><?= $plan['store_count'] ?> bolt</strong></li>
                    <?php } ?>

                    <?php if ($plan['milestone_limit'] === null) { ?>
                        <li>✓ Korlátlan mérföldkő</li>
                    <?php } else { ?>
                        <li>✓ Legfeljebb <strong><?= $plan['milestone_limit'] ?> mérföldkő</strong></li>
                    <?php } ?>

                    <?php if ($plan['staff_limit'] === null) { ?>
                        <li>✓ Korlátlan munkatárs</li>
                    <?php } else { ?>
                        <li>✓ Boltonként <strong><?= $plan['staff_limit'] ?> munkatárs</strong></li>
                    <?php } ?>

                    <?php if ($plan['allow_shared_db'] == 1) { ?>
                        <li>✓ <strong>Boltok összekapcsolása</strong> (közös pontok)</li>
                    <?php } ?>

                    <?php if ($plan['allow_events'] == 1) { ?>
                        <li>✓ <strong>Események</strong> (pl. dupla pontok)</li>
                    <?php } ?>

                    <li>✓ Egyedi üzenetek</li>

                    <?php if ($plan['allow_priority_support'] == 1) { ?>
                        <li>✓ Kiemelt ügyféltámogatás</li>
                    <?php } ?>
                </ul>

                <input type="submit" value="Kiválasztás">
            </form>
        <?php } ?>
    </div>
</body>
</html>
