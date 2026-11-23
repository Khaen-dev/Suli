<?php

if (!isset($conn, $storeId, $userId, $isActive, $pointOwner, $tabUrl, $message)) {
    header("Location: ../mystores.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isActive) {
    $email = $_POST['email'];
    $points = (int)$_POST['points'];
    $note = $_POST['note'];

    $customer = queryOne($conn, "SELECT id FROM users WHERE email = ?", [$email]);

    if ($customer === null) {
        $message = "Nincs ilyen e-mail címmel regisztrált felhasználó.";
    } else {
        $card = queryOne($conn, "SELECT * FROM loyalty_cards WHERE user_id = ? AND {$pointOwner['column']} = ?", [$customer['id'], $pointOwner['id']]);

        $currentPoints = 0;
        if ($card !== null) {
            $currentPoints = $card['points'];
        }
        $newPoints = $currentPoints + $points;

        if ($newPoints < 0) {
            $message = "A vásárlónak nincs ennyi pontja.";
        } else {
            if ($card === null) {
                $qrCode = bin2hex(random_bytes(16));
                $cardId = runQuery($conn, "INSERT INTO loyalty_cards (user_id, {$pointOwner['column']}, qr_token) VALUES (?, ?, ?)", [$customer['id'], $pointOwner['id'], $qrCode]);
                $card = queryOne($conn, "SELECT * FROM loyalty_cards WHERE id = ?", [$cardId]);
            }

            runQuery($conn, "UPDATE loyalty_cards SET points = ? WHERE id = ?", [$newPoints, $card['id']]);
            runQuery($conn, "
                INSERT INTO point_transactions (card_id, store_id, staff_user_id, type, points_change, balance_after, note)
                VALUES (?, ?, ?, 'manual', ?, ?, ?)
            ", [$card['id'], $storeId, $userId, $points, $newPoints, $note]);
            $message = "Pontok elmentve. Új egyenleg: " . $newPoints;
        }
    }
}

$customers = queryAll($conn, "
    SELECT loyalty_cards.points, users.last_name, users.first_name, users.email
    FROM loyalty_cards
    JOIN users ON users.id = loyalty_cards.user_id
    WHERE loyalty_cards.{$pointOwner['column']} = ?
      AND EXISTS (SELECT 1 FROM point_transactions WHERE point_transactions.card_id = loyalty_cards.id)
    ORDER BY users.last_name
", [$pointOwner['id']]);

$milestones = queryAll($conn, "SELECT * FROM milestones WHERE {$pointOwner['column']} = ? ORDER BY points_required", [$pointOwner['id']]);
?>

<h2>Vásárlók</h2>

<?php if ($message !== "") { ?>
    <div class="message"><?= $message ?></div>
<?php } ?>

<?php if ($isActive) { ?>
    <h3>Pontok jóváírása / levonása</h3>
    <form method="post" action="<?= $tabUrl ?>" class="dash-form">
        <label>Vásárló e-mail címe<input type="email" name="email" required></label>
        <label>Pont (levonáshoz negatív szám)<input type="number" name="points" required></label>
        <label class="wide">Megjegyzés<input type="text" name="note" placeholder="Pl.: vásárlás, jutalom beváltása"></label>
        <button type="submit" class="btn">Mentés</button>
    </form>
<?php } ?>

<h3 class="form-title">Pontgyűjtő vásárlók</h3>
<?php if (count($customers) === 0) { ?>
    <p class="muted">Még egy vásárló sem gyűjt pontot.</p>
<?php } else { ?>
    <table class="dash-table">
        <tr><th>Név</th><th>E-mail</th><th>Pont</th><th>Beváltható jutalmak</th></tr>
        <?php foreach ($customers as $customer) { ?>
            <tr>
                <td><?= $customer['last_name'] ?> <?= $customer['first_name'] ?></td>
                <td><?= $customer['email'] ?></td>
                <td><?= $customer['points'] ?></td>
                <td>
                    <?php foreach ($milestones as $milestone) { ?>
                        <?php if ($customer['points'] >= $milestone['points_required']) { ?>
                            <span class="badge"><?= $milestone['title'] ?> (<?= $milestone['points_required'] ?> pont)</span>
                        <?php } ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>
