<?php

require "required/config.php";
require "required/favo.php";

$userId = getLoggedInUserId();
$subscription = getSubscription($conn, $userId);

$ownedStores = queryAll($conn, "SELECT * FROM stores WHERE owner_userid = ? ORDER BY name", [$userId]);

$staffStores = queryAll($conn, "
    SELECT stores.*, store_staff.role
    FROM store_staff
    JOIN stores ON stores.id = store_staff.store_id
    WHERE store_staff.user_id = ?
    ORDER BY stores.name
", [$userId]);

$canAddStore = false;
if ($subscription['is_active']) {
    $canAddStore = $subscription['store_count'] === null || count($ownedStores) < $subscription['store_count'];
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saját boltok</title>
    <script src="https://kit.fontawesome.com/086e7cefb3.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/boti.css">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>
    <?php require "required/nav.php"; ?>

    <div class="stores-container">
        <?php if ($subscription['has_subscription']) { ?>
            <p class="plan-line">
                Csomag: <strong><?= $subscription['name'] ?></strong>,
                lejár: <?= $subscription['expires'] ?>
                · <a href="pricing.php">Csomag módosítása</a>
            </p>
        <?php } ?>

        <?php foreach ($ownedStores as $store) { ?>
            <a class="store-card" href="dashboard.php?id=<?= $store['id'] ?>">
                <p class="store-name"><?= $store['name'] ?></p>
                <p class="store-type"><?= $store['type'] ?></p>
                <p class="store-address"><?= $store['address'] ?></p>
                <span class="store-button">Kezelés</span>
            </a>
        <?php } ?>

        <?php if ($canAddStore) { ?>
            <a class="store-card add" href="addstore.php"><i class="fa-solid fa-plus"></i></a>
        <?php } else { ?>
            <a class="store-card add" href="pricing.php"><i class="fa-solid fa-plus"></i></a>
        <?php } ?>

        <?php if (count($staffStores) > 0) { ?>
            <h2 class="section-title">Boltok, ahol munkatárs vagy</h2>
            <?php foreach ($staffStores as $store) { ?>
                <a class="store-card" href="dashboard.php?id=<?= $store['id'] ?>">
                    <p class="store-name"><?= $store['name'] ?></p>
                    <p class="store-type"><?= $store['type'] ?></p>
                    <p class="store-address"><?= ROLE_NAMES[$store['role']] ?></p>
                    <span class="store-button">Kezelés</span>
                </a>
            <?php } ?>
        <?php } ?>
    </div>
</body>
</html>
