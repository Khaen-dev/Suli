<?php

require "required/config.php";
require "required/favo.php";

$userId = getLoggedInUserId();
$storeId = $_POST['store_id'];

$store = queryOne($conn, "SELECT * FROM stores WHERE id = ? AND owner_userid = ?", [$storeId, $userId]);

if ($store !== null) {
    runQuery($conn, "DELETE FROM stores WHERE id = ?", [$storeId]);

    if ($store['group_id'] !== null) {
        $storesLeft = queryOne($conn, "SELECT COUNT(*) AS count FROM stores WHERE group_id = ?", [$store['group_id']]);
        if ($storesLeft['count'] == 0) {
            runQuery($conn, "DELETE FROM store_groups WHERE id = ?", [$store['group_id']]);
        }
    }
}

header("Location: mystores.php");
exit();
