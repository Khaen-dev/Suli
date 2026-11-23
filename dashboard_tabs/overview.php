<?php

if (!isset($conn, $store, $storeId, $pointOwner, $subscription)) {
    header("Location: ../mystores.php");
    exit();
}

$customerCount = queryOne($conn, "
    SELECT COUNT(*) AS count FROM loyalty_cards
    WHERE {$pointOwner['column']} = ?
      AND EXISTS (SELECT 1 FROM point_transactions WHERE point_transactions.card_id = loyalty_cards.id)
", [$pointOwner['id']]);
$milestoneCount = queryOne($conn, "SELECT COUNT(*) AS count FROM milestones WHERE {$pointOwner['column']} = ?", [$pointOwner['id']]);
$staffCount = queryOne($conn, "SELECT COUNT(*) AS count FROM store_staff WHERE store_id = ?", [$storeId]);

$recentTransactions = queryAll($conn, "
    SELECT point_transactions.*, users.last_name, users.first_name
    FROM point_transactions
    JOIN loyalty_cards ON loyalty_cards.id = point_transactions.card_id
    JOIN users ON users.id = loyalty_cards.user_id
    WHERE point_transactions.store_id = ?
    ORDER BY point_transactions.created_at DESC
    LIMIT 10
", [$storeId]);
?>

<h2>Áttekintés</h2>

<div class="stat-grid">
    <div class="stat">
        <span class="stat-value"><?= $customerCount['count'] ?></span>
        <span class="stat-label">Törzsvásárló</span>
    </div>
    <div class="stat">
        <span class="stat-value"><?= $milestoneCount['count'] ?></span>
        <span class="stat-label">Mérföldkő</span>
    </div>
    <div class="stat">
        <span class="stat-value"><?= $staffCount['count'] ?></span>
        <span class="stat-label">Munkatárs</span>
    </div>
</div>

<div class="two-col">
    <div class="panel">
        <h3>Bolt adatai</h3>
        <ul class="info-list">
            <li><span>Cím</span><?= $store['address'] ?></li>
            <li><span>Kapcsolattartó</span><?= $store['contact_name'] ?> (<?= $store['contact_email'] ?>)</li>
            <li><span>Telefonszám</span><?= $store['tel'] ?></li>
            <?php if ($subscription['has_subscription']) { ?>
                <li><span>Csomag</span><?= $subscription['name'] ?> (lejár: <?= $subscription['expires'] ?>)</li>
            <?php } ?>
        </ul>
    </div>

    <div class="panel">
        <h3>Legutóbbi pontmozgások</h3>
        <?php if (count($recentTransactions) === 0) { ?>
            <p class="muted">Még nincs pontmozgás.</p>
        <?php } else { ?>
            <table class="dash-table">
                <tr><th>Időpont</th><th>Vásárló</th><th>Pont</th><th>Megjegyzés</th></tr>
                <?php foreach ($recentTransactions as $transaction) { ?>
                    <tr>
                        <td><?= $transaction['created_at'] ?></td>
                        <td><?= $transaction['last_name'] ?> <?= $transaction['first_name'] ?></td>
                        <td><?= $transaction['points_change'] ?></td>
                        <td><?= $transaction['note'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
</div>
