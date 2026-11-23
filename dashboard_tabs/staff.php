<?php

if (!isset($conn, $store, $storeId, $isActive, $tabUrl, $message, $subscription)) {
    header("Location: ../mystores.php");
    exit();
}

$staffRoles = ['pultos', 'szerkeszto', 'menedzser'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isActive) {
    if ($_POST['action'] === 'add') {
        $newStaff = queryOne($conn, "SELECT id FROM users WHERE email = ?", [$_POST['email']]);
        $staffCount = queryOne($conn, "SELECT COUNT(*) AS count FROM store_staff WHERE store_id = ?", [$storeId]);
        $limit = $subscription['staff_limit'];

        if ($newStaff === null) {
            $message = "Nincs ilyen e-mail címmel regisztrált felhasználó.";
        } elseif ($newStaff['id'] == $store['owner_userid']) {
            $message = "A tulajdonost nem kell hozzáadni.";
        } elseif ($limit !== null && $staffCount['count'] >= $limit) {
            $message = "A csomagod legfeljebb " . $limit . " munkatársat enged.";
        } else {
            runQuery($conn, "INSERT IGNORE INTO store_staff (store_id, user_id, role) VALUES (?, ?, ?)", [$storeId, $newStaff['id'], $_POST['role']]);
            $message = "Munkatárs hozzáadva.";
        }
    }

    if ($_POST['action'] === 'change_role') {
        runQuery($conn, "UPDATE store_staff SET role = ? WHERE id = ? AND store_id = ?", [$_POST['role'], $_POST['staff_id'], $storeId]);
        $message = "Rang módosítva.";
    }

    if ($_POST['action'] === 'remove') {
        runQuery($conn, "DELETE FROM store_staff WHERE id = ? AND store_id = ?", [$_POST['staff_id'], $storeId]);
        $message = "Munkatárs eltávolítva.";
    }
}

$staffMembers = queryAll($conn, "
    SELECT store_staff.id, store_staff.role, users.last_name, users.first_name, users.email
    FROM store_staff
    JOIN users ON users.id = store_staff.user_id
    WHERE store_staff.store_id = ?
", [$storeId]);
?>

<h2>Munkatársak</h2>
<p>
    Pultos: pontok kezelése.
    Szerkesztő: pontok, mérföldkövek, események, üzenetek.
    Menedzser: minden, kivéve a csoportokat és a bolt törlését.
</p>

<?php if ($message !== "") { ?>
    <div class="message"><?= $message ?></div>
<?php } ?>

<?php if (count($staffMembers) === 0) { ?>
    <p class="muted">Még nincs munkatárs.</p>
<?php } else { ?>
    <table class="dash-table">
        <tr><th>Név</th><th>E-mail</th><th>Rang</th><th></th></tr>
        <?php foreach ($staffMembers as $staffMember) { ?>
            <tr>
                <td><?= $staffMember['last_name'] ?> <?= $staffMember['first_name'] ?></td>
                <td><?= $staffMember['email'] ?></td>
                <td>
                    <form method="post" action="<?= $tabUrl ?>" class="inline-form">
                        <input type="hidden" name="action" value="change_role">
                        <input type="hidden" name="staff_id" value="<?= $staffMember['id'] ?>">
                        <select name="role">
                            <?php foreach ($staffRoles as $staffRole) { ?>
                                <option value="<?= $staffRole ?>" <?= $staffRole === $staffMember['role'] ? 'selected' : '' ?>><?= ROLE_NAMES[$staffRole] ?></option>
                            <?php } ?>
                        </select>
                        <?php if ($isActive) { ?>
                            <button type="submit" class="btn">Mentés</button>
                        <?php } ?>
                    </form>
                </td>
                <td>
                    <?php if ($isActive) { ?>
                        <form method="post" action="<?= $tabUrl ?>">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="staff_id" value="<?= $staffMember['id'] ?>">
                            <button type="submit" class="btn btn-red"><i class="fa-solid fa-user-minus"></i></button>
                        </form>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>

<?php if ($isActive) { ?>
    <h3 class="form-title">Munkatárs hozzáadása</h3>
    <form method="post" action="<?= $tabUrl ?>" class="dash-form">
        <input type="hidden" name="action" value="add">
        <label>Felhasználó e-mail címe<input type="email" name="email" required></label>
        <label>Rang
            <select name="role">
                <?php foreach ($staffRoles as $staffRole) { ?>
                    <option value="<?= $staffRole ?>"><?= ROLE_NAMES[$staffRole] ?></option>
                <?php } ?>
            </select>
        </label>
        <button type="submit" class="btn">Hozzáadás</button>
    </form>
<?php } ?>
