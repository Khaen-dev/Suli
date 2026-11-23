<?php

if (!isset($conn, $isActive, $pointOwner, $tabUrl, $message, $subscription)) {
    header("Location: ../mystores.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isActive) {
    if ($_POST['action'] === 'add') {
        $milestoneCount = queryOne($conn, "SELECT COUNT(*) AS count FROM milestones WHERE {$pointOwner['column']} = ?", [$pointOwner['id']]);
        $limit = $subscription['milestone_limit'];

        if ($limit !== null && $milestoneCount['count'] >= $limit) {
            $message = "A csomagod legfeljebb " . $limit . " mérföldkövet enged.";
        } else {
            runQuery($conn, "
                INSERT INTO milestones ({$pointOwner['column']}, title, points_required, reward_description, customer_message, cashier_message)
                VALUES (?, ?, ?, ?, ?, ?)
            ", [$pointOwner['id'], $_POST['title'], $_POST['points_required'], $_POST['reward_description'], $_POST['customer_message'], $_POST['cashier_message']]);
            $message = "Mérföldkő hozzáadva.";
        }
    }

    if ($_POST['action'] === 'delete') {
        runQuery($conn, "DELETE FROM milestones WHERE id = ? AND {$pointOwner['column']} = ?", [$_POST['milestone_id'], $pointOwner['id']]);
        $message = "Mérföldkő törölve.";
    }
}

$milestones = queryAll($conn, "SELECT * FROM milestones WHERE {$pointOwner['column']} = ? ORDER BY points_required", [$pointOwner['id']]);
?>

<h2>Mérföldkövek</h2>
<p>Ha a vásárló eléri a megadott pontszámot, megkapja a jutalmat.</p>

<?php if ($message !== "") { ?>
    <div class="message"><?= $message ?></div>
<?php } ?>

<?php if (count($milestones) === 0) { ?>
    <p class="muted">Még nincs mérföldkő.</p>
<?php } else { ?>
    <table class="dash-table">
        <tr><th>Név</th><th>Pont</th><th>Jutalom</th><th>Üzenet a vásárlónak</th><th>Üzenet a pultosnak</th><th></th></tr>
        <?php foreach ($milestones as $milestone) { ?>
            <tr>
                <td><?= $milestone['title'] ?></td>
                <td><?= $milestone['points_required'] ?></td>
                <td><?= $milestone['reward_description'] ?></td>
                <td><?= $milestone['customer_message'] ?></td>
                <td><?= $milestone['cashier_message'] ?></td>
                <td>
                    <?php if ($isActive) { ?>
                        <form method="post" action="<?= $tabUrl ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="milestone_id" value="<?= $milestone['id'] ?>">
                            <button type="submit" class="btn btn-red"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>

<?php if ($isActive) { ?>
    <h3 class="form-title">Új mérföldkő</h3>
    <form method="post" action="<?= $tabUrl ?>" class="dash-form">
        <input type="hidden" name="action" value="add">
        <label>Név<input type="text" name="title" required placeholder="Pl.: Ingyen kávé"></label>
        <label>Szükséges pont<input type="number" name="points_required" min="1" required></label>
        <label class="wide">Jutalom<input type="text" name="reward_description" required placeholder="Pl.: Egy kávé ingyen"></label>
        <label class="wide">Üzenet a vásárlónak<input type="text" name="customer_message"></label>
        <label class="wide">Üzenet a pultosnak<input type="text" name="cashier_message"></label>
        <button type="submit" class="btn">Hozzáadás</button>
    </form>
<?php } ?>
