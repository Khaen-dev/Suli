<?php

if (!isset($conn, $store, $storeId, $userId, $role, $isActive, $tabUrl, $message, $subscription)) {
    header("Location: ../mystores.php");
    exit();
}

$isOwner = $role === 'tulajdonos';
$groupsAllowed = $subscription['allow_shared_db'] == 1;

function moveStoreIntoGroup($conn, $storeId, $groupId)
{
    $storeCards = queryAll($conn, "SELECT * FROM loyalty_cards WHERE store_id = ?", [$storeId]);

    foreach ($storeCards as $storeCard) {
        $groupCard = queryOne($conn, "SELECT * FROM loyalty_cards WHERE user_id = ? AND group_id = ?", [$storeCard['user_id'], $groupId]);

        if ($groupCard === null) {
            runQuery($conn, "UPDATE loyalty_cards SET store_id = NULL, group_id = ? WHERE id = ?", [$groupId, $storeCard['id']]);
        } else {
            runQuery($conn, "UPDATE loyalty_cards SET points = points + ? WHERE id = ?", [$storeCard['points'], $groupCard['id']]);
            runQuery($conn, "UPDATE point_transactions SET card_id = ? WHERE card_id = ?", [$groupCard['id'], $storeCard['id']]);
            runQuery($conn, "DELETE FROM loyalty_cards WHERE id = ?", [$storeCard['id']]);
        }
    }

    runQuery($conn, "UPDATE milestones SET origin_store_id = store_id, store_id = NULL, group_id = ? WHERE store_id = ?", [$groupId, $storeId]);
    runQuery($conn, "UPDATE stores SET group_id = ? WHERE id = ?", [$groupId, $storeId]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isActive) {
    if ($_POST['action'] === 'save') {
        runQuery($conn, "
            UPDATE stores SET name = ?, type = ?, address = ?, contact_name = ?, contact_email = ?, tel = ?
            WHERE id = ?
        ", [$_POST['name'], $_POST['type'], $_POST['address'], $_POST['contact_name'], $_POST['contact_email'], $_POST['tel'], $storeId]);
        $message = "A bolt adatai elmentve.";
    }

    if ($_POST['action'] === 'create_group' && $isOwner && $groupsAllowed) {
        $groupId = runQuery($conn, "INSERT INTO store_groups (owner_userid, name) VALUES (?, ?)", [$userId, $_POST['group_name']]);
        moveStoreIntoGroup($conn, $storeId, $groupId);
        $message = "A csoport létrejött.";
    }

    if ($_POST['action'] === 'join_group' && $isOwner && $groupsAllowed) {
        moveStoreIntoGroup($conn, $storeId, $_POST['group_id']);
        $message = "A bolt csatlakozott a csoporthoz.";
    }

    if ($_POST['action'] === 'leave_group' && $isOwner) {
        $groupId = $store['group_id'];
        $otherStores = queryOne($conn, "SELECT COUNT(*) AS count FROM stores WHERE group_id = ? AND id <> ?", [$groupId, $storeId]);

        runQuery($conn, "UPDATE milestones SET store_id = ?, group_id = NULL, origin_store_id = NULL WHERE group_id = ? AND origin_store_id = ?", [$storeId, $groupId, $storeId]);

        if ($otherStores['count'] == 0) {
            runQuery($conn, "UPDATE loyalty_cards SET group_id = NULL, store_id = ? WHERE group_id = ?", [$storeId, $groupId]);
            runQuery($conn, "UPDATE milestones SET store_id = ?, group_id = NULL, origin_store_id = NULL WHERE group_id = ?", [$storeId, $groupId]);
        }

        runQuery($conn, "UPDATE stores SET group_id = NULL WHERE id = ?", [$storeId]);

        if ($otherStores['count'] == 0) {
            runQuery($conn, "DELETE FROM store_groups WHERE id = ?", [$groupId]);
        }
        $message = "A bolt kilépett a csoportból.";
    }

    $store = queryOne($conn, "SELECT * FROM stores WHERE id = ?", [$storeId]);
}

$group = null;
$groupStores = [];
$ownerGroups = [];
if ($store['group_id'] !== null) {
    $group = queryOne($conn, "SELECT * FROM store_groups WHERE id = ?", [$store['group_id']]);
    $groupStores = queryAll($conn, "SELECT name, address FROM stores WHERE group_id = ?", [$store['group_id']]);
} else {
    $ownerGroups = queryAll($conn, "SELECT * FROM store_groups WHERE owner_userid = ?", [$userId]);
}
?>

<h2>Beállítások</h2>

<?php if ($message !== "") { ?>
    <div class="message"><?= $message ?></div>
<?php } ?>

<h3>Bolt adatai</h3>
<form method="post" action="<?= $tabUrl ?>" class="dash-form">
    <input type="hidden" name="action" value="save">
    <label>Bolt neve<input type="text" name="name" required value="<?= $store['name'] ?>"></label>
    <label>Típus<input type="text" name="type" required value="<?= $store['type'] ?>"></label>
    <label class="wide">Cím<input type="text" name="address" required value="<?= $store['address'] ?>"></label>
    <label>Kapcsolattartó<input type="text" name="contact_name" required value="<?= $store['contact_name'] ?>"></label>
    <label>Kapcsolattartó e-mail<input type="email" name="contact_email" required value="<?= $store['contact_email'] ?>"></label>
    <label>Telefonszám<input type="tel" name="tel" required value="<?= $store['tel'] ?>"></label>
    <?php if ($isActive) { ?>
        <button type="submit" class="btn">Mentés</button>
    <?php } ?>
</form>

<?php if ($isOwner) { ?>
    <h3 class="form-title">Boltok összekapcsolása</h3>

    <?php if ($group !== null) { ?>
        <p>A bolt a(z) <strong><?= $group['name'] ?></strong> csoport tagja. A pontok közösek ezekben a boltokban:</p>
        <ul class="group-list">
            <?php foreach ($groupStores as $groupStore) { ?>
                <li><?= $groupStore['name'] ?> – <?= $groupStore['address'] ?></li>
            <?php } ?>
        </ul>
        <?php if ($isActive) { ?>
            <form method="post" action="<?= $tabUrl ?>">
                <input type="hidden" name="action" value="leave_group">
                <button type="submit" class="btn btn-gray">Kilépés a csoportból</button>
            </form>
        <?php } ?>
    <?php } elseif (!$groupsAllowed) { ?>
        <p class="error-text">A boltok összekapcsolása a Haladó és Üzleti csomagban érhető el.</p>
    <?php } elseif ($isActive) { ?>
        <p>Összekapcsolt boltokban a vásárlók pontjai és a mérföldkövek közösek.</p>
        <div class="two-col">
            <form method="post" action="<?= $tabUrl ?>" class="dash-form">
                <input type="hidden" name="action" value="create_group">
                <label class="wide">Új csoport neve<input type="text" name="group_name" required></label>
                <button type="submit" class="btn">Csoport létrehozása</button>
            </form>

            <?php if (count($ownerGroups) > 0) { ?>
                <form method="post" action="<?= $tabUrl ?>" class="dash-form">
                    <input type="hidden" name="action" value="join_group">
                    <label class="wide">Meglévő csoport
                        <select name="group_id">
                            <?php foreach ($ownerGroups as $ownerGroup) { ?>
                                <option value="<?= $ownerGroup['id'] ?>"><?= $ownerGroup['name'] ?></option>
                            <?php } ?>
                        </select>
                    </label>
                    <button type="submit" class="btn">Csatlakozás</button>
                </form>
            <?php } ?>
        </div>
    <?php } ?>

    <h3 class="form-title">Bolt törlése</h3>
    <form method="post" action="deletestore.php" onsubmit="return confirm('Biztosan törlöd a boltot?')">
        <input type="hidden" name="store_id" value="<?= $storeId ?>">
        <button type="submit" class="btn btn-red">Bolt törlése</button>
    </form>
<?php } ?>
