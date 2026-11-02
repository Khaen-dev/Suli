<?php 

require "required/config.php";

if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    die();
}

$userId = $_COOKIE['id'];
$store_count = 0;
$hasActiveSub = false;
$canAddStore = false;
$expired = false;

$subStmt = $conn->prepare("
    SELECT subs.*, pricing.store_count AS max_stores 
    FROM user_subscriptions as subs
    JOIN pricing ON subs.subscription = pricing.id 
    WHERE subs.userid = ?
");
$subStmt->bind_param("i", $userId);
$subStmt->execute();
$subResult = $subStmt->get_result();

if ($subRow = $subResult->fetch_assoc()) {
    $hasActiveSub = true;
    $maxStores = $subRow['max_stores'];
    if (strtotime($subRow['expires']) < time()) {
        $expired = true;
    }
}
$subStmt->close();

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boltjaim</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/086e7cefb3.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/boti.css">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>
    <?php require "required/nav.php"; ?>

    <div class="stores-container">
        <?php if ($hasActiveSub) {
            $stmt = $conn->prepare("SELECT * FROM stores WHERE userid = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $store_count = $result->num_rows;

            while ($store = $result->fetch_assoc()) { ?>
                <form class="store-card" method="post" action="dashboard.php?id=<?= $store['id']; ?>">
                    <p class="store-name"><?= htmlspecialchars($store['name']) ?></p>
                    <p class="store-type"><?= htmlspecialchars($store['type']) ?></p>
                    <p class="store-address"><?= htmlspecialchars($store['address']) ?></p>
                    <input type="submit" name="store-handler-btn" value="Kezelés">
                </form>
            <?php 
            }
            $stmt->close();

            if (!$expired && (is_null($maxStores) || $maxStores > $store_count)) {
                $canAddStore = true;
            }
            ?>

            <?php if ($expired) { ?>
                <div class="store-card add" id="add-card" onclick="Add(2)" title="Lejárt az előfizetésed!">
                    <span><i class="fa-solid fa-plus"></i></span>
                </div>
            <?php } elseif ($canAddStore) { ?>
                <div class="store-card add" id="add-card" onclick="Add(1)">
                    <span><i class="fa-solid fa-plus"></i></span>
                </div>
            <?php } else { ?>
                <div class="store-card add-disabled" id="add-card">
                    <span><i class="fa-solid fa-plus"></i></span>
                </div>
            <?php } ?>

        <?php } else { ?>
            <div class="store-card add" id="add-card" onclick="Add(0)">
                <span><i class="fa-solid fa-plus"></i></span>
            </div>
        <?php } ?>
    </div>

</body>
</html>
<script>
    function Add(sub) {
        if (sub == 1) {
            window.location.href = "addstore.php";
        } else if (sub == 2) {
            window.location.href = "pricing.php?expired=true";
        }
        else if (sub == 0) {
            window.location.href = "pricing.php";
        }
    }
</script>