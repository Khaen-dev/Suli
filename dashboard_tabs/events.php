<?php

if (!isset($conn, $storeId, $userId, $isActive, $tabUrl, $message, $subscription)) {
    header("Location: ../mystores.php");
    exit();
}

$eventsAllowed = $subscription['allow_events'] == 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isActive && $eventsAllowed) {
    if ($_POST['action'] === 'add') {
        runQuery($conn, "
            INSERT INTO events (store_id, title, description, multiplier, bonus_points, starts_at, ends_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ", [$storeId, $_POST['title'], $_POST['description'], $_POST['multiplier'], $_POST['bonus_points'], $_POST['starts_at'], $_POST['ends_at'], $userId]);
        $message = "Esemény létrehozva.";
    }

    if ($_POST['action'] === 'delete') {
        runQuery($conn, "DELETE FROM events WHERE id = ? AND store_id = ?", [$_POST['event_id'], $storeId]);
        $message = "Esemény törölve.";
    }
}

$events = queryAll($conn, "SELECT * FROM events WHERE store_id = ? ORDER BY starts_at DESC", [$storeId]);
?>

<h2>Események</h2>

<?php if (!$eventsAllowed) { ?>
    <p class="error-text">Az események a Haladó és Üzleti csomagban érhetők el.</p>
<?php } else { ?>
    <p>Az esemény ideje alatt a vásárlások a megadott szorzóval és bónusz ponttal számolnak (pl. dupla pontok napja).</p>

    <?php if ($message !== "") { ?>
        <div class="message"><?= $message ?></div>
    <?php } ?>

    <?php if (count($events) === 0) { ?>
        <p class="muted">Még nincs esemény.</p>
    <?php } else { ?>
        <table class="dash-table">
            <tr><th>Név</th><th>Leírás</th><th>Kezdés</th><th>Befejezés</th><th>Szorzó</th><th>Bónusz pont</th><th></th></tr>
            <?php foreach ($events as $event) { ?>
                <tr>
                    <td><?= $event['title'] ?></td>
                    <td><?= $event['description'] ?></td>
                    <td><?= $event['starts_at'] ?></td>
                    <td><?= $event['ends_at'] ?></td>
                    <td><?= $event['multiplier'] ?>×</td>
                    <td><?= $event['bonus_points'] ?></td>
                    <td>
                        <?php if ($isActive) { ?>
                            <form method="post" action="<?= $tabUrl ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <button type="submit" class="btn btn-red"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>

    <?php if ($isActive) { ?>
        <h3 class="form-title">Új esemény</h3>
        <form method="post" action="<?= $tabUrl ?>" class="dash-form">
            <input type="hidden" name="action" value="add">
            <label class="wide">Név<input type="text" name="title" required placeholder="Pl.: Dupla pontok hétfője"></label>
            <label class="wide">Leírás<input type="text" name="description"></label>
            <label>Kezdés<input type="datetime-local" name="starts_at" required></label>
            <label>Befejezés<input type="datetime-local" name="ends_at" required></label>
            <label>Pontszorzó<input type="number" name="multiplier" min="1" max="10" value="2" required></label>
            <label>Bónusz pont<input type="number" name="bonus_points" min="0" value="0" required></label>
            <button type="submit" class="btn">Létrehozás</button>
        </form>
    <?php } ?>
<?php } ?>
