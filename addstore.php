<?php

require "required/config.php";
require "required/favo.php";

$userId = getLoggedInUserId();
$subscription = getSubscription($conn, $userId);

if (!$subscription['is_active']) {
    header("Location: pricing.php");
    exit();
}

$storeCount = queryOne($conn, "SELECT COUNT(*) AS count FROM stores WHERE owner_userid = ?", [$userId]);
if ($subscription['store_count'] !== null && $storeCount['count'] >= $subscription['store_count']) {
    header("Location: pricing.php");
    exit();
}

if (isset($_POST['store_name'])) {
    $storeType = $_POST['store_type'];
    if ($storeType === 'Egyéb') {
        $storeType = $_POST['other_type'];
    }

    $newStoreId = runQuery($conn, "
        INSERT INTO stores (owner_userid, name, type, address, contact_name, contact_email, tel)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ", [$userId, $_POST['store_name'], $storeType, $_POST['store_address'], $_POST['contact_name'], $_POST['contact_email'], $_POST['tel']]);

    header("Location: dashboard.php?id=" . $newStoreId);
    exit();
}

$storeTypes = ['Kávézó', 'Pékség', 'Cukrászda', 'Fodrászat', 'Szépségszalon', 'Étterem', 'Pizzázó', 'Bár', 'Egyéb'];
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bolt hozzáadása</title>
    <link rel="stylesheet" href="css/boti.css">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>
    <?php require "required/nav.php"; ?>

    <div class="form-container">
        <h2>Bolt hozzáadása</h2>

        <form method="post">
            <label for="store_name">Bolt neve *</label>
            <input type="text" name="store_name" id="store_name" required>

            <label for="store_type">Bolt típusa *</label>
            <select name="store_type" id="store_type" required>
                <?php foreach ($storeTypes as $storeType) { ?>
                    <option value="<?= $storeType ?>"><?= $storeType ?></option>
                <?php } ?>
            </select>

            <label for="other_type">Ha "Egyéb": a bolt típusa</label>
            <input type="text" name="other_type" id="other_type" placeholder="Pl.: Virágbolt">

            <label for="store_address">Bolt címe *</label>
            <input type="text" name="store_address" id="store_address" required>

            <label for="contact_name">Kapcsolattartó neve *</label>
            <input type="text" name="contact_name" id="contact_name" required>

            <label for="contact_email">Kapcsolattartó e-mail *</label>
            <input type="email" name="contact_email" id="contact_email" required>

            <label for="tel">Telefonszám *</label>
            <input type="tel" name="tel" id="tel" required>

            <label class="checkbox-label"><input type="checkbox" required> ÁSZF elfogadása *</label>
            <label class="checkbox-label"><input type="checkbox" required> Adatkezelési tájékoztató elfogadása *</label>

            <input type="submit" value="Bolt hozzáadása">
        </form>
    </div>
</body>
</html>
