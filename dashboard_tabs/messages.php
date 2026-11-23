<?php

if (!isset($conn, $storeId, $isActive, $tabUrl, $message)) {
    header("Location: ../mystores.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isActive) {
    if ($_POST['action'] === 'add') {
        runQuery($conn, "INSERT INTO store_messages (store_id, trigger_type, audience, message) VALUES (?, ?, ?, ?)",
            [$storeId, $_POST['trigger_type'], $_POST['audience'], $_POST['message']]);
        $message = "Üzenet hozzáadva.";
    }

    if ($_POST['action'] === 'delete') {
        runQuery($conn, "DELETE FROM store_messages WHERE id = ? AND store_id = ?", [$_POST['message_id'], $storeId]);
        $message = "Üzenet törölve.";
    }
}

$storeMessages = queryAll($conn, "SELECT * FROM store_messages WHERE store_id = ? ORDER BY id", [$storeId]);

$triggerNames = ['first_scan' => 'Első vásárláskor', 'every_scan' => 'Minden vásárláskor'];
$audienceNames = ['customer' => 'Vásárló', 'cashier' => 'Pultos'];
?>

<h2>Egyedi üzenetek</h2>
<p>Ezek az üzenetek pontgyűjtéskor jelennek meg a pultosnak vagy a vásárlónak.</p>

<?php if ($message !== "") { ?>
    <div class="message"><?= $message ?></div>
<?php } ?>

<?php if (count($storeMessages) === 0) { ?>
    <p class="muted">Még nincs egyedi üzenet.</p>
<?php } else { ?>
    <table class="dash-table">
        <tr><th>Mikor</th><th>Kinek</th><th>Üzenet</th><th></th></tr>
        <?php foreach ($storeMessages as $storeMessage) { ?>
            <tr>
                <td><?= $triggerNames[$storeMessage['trigger_type']] ?></td>
                <td><?= $audienceNames[$storeMessage['audience']] ?></td>
                <td><?= $storeMessage['message'] ?></td>
                <td>
                    <?php if ($isActive) { ?>
                        <form method="post" action="<?= $tabUrl ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="message_id" value="<?= $storeMessage['id'] ?>">
                            <button type="submit" class="btn btn-red"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>

<?php if ($isActive) { ?>
    <h3 class="form-title">Új üzenet</h3>
    <form method="post" action="<?= $tabUrl ?>" class="dash-form">
        <input type="hidden" name="action" value="add">
        <label>Mikor
            <select name="trigger_type">
                <option value="every_scan">Minden vásárláskor</option>
                <option value="first_scan">Első vásárláskor</option>
            </select>
        </label>
        <label>Kinek
            <select name="audience">
                <option value="customer">Vásárlónak</option>
                <option value="cashier">Pultosnak</option>
            </select>
        </label>
        <label class="wide">Üzenet<input type="text" name="message" required placeholder="Pl.: Köszönjük a vásárlást!"></label>
        <button type="submit" class="btn">Hozzáadás</button>
    </form>
<?php } ?>
