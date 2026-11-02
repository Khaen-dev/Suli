<?php 

require "required/config.php";

if (!isset($_COOKIE['id'])) {
    header("Location: index.php");
    die();
}

$userId = $_COOKIE['id'];

if (isset($_POST['select_plan_btn'])) {
    $planId = $_POST['plan_id'];

    $checkStmt = $conn->prepare("SELECT id FROM user_subscriptions WHERE userid = ?");
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

    if ($checkResult->num_rows > 0) {
        $updateStmt = $conn->prepare("UPDATE user_subscriptions SET subscription = ?, expires = ? WHERE userid = ?");
        $updateStmt->bind_param("isi", $planId, $expiresAt, $userId);
        $updateStmt->execute();
        $updateStmt->close();
    } else {
        $insertStmt = $conn->prepare("INSERT INTO user_subscriptions (userid, subscription, expires) VALUES (?, ?, ?)");
        $insertStmt->bind_param("iis", $userId, $planId, $expiresAt);
        $insertStmt->execute();
        $insertStmt->close();
    }
    
    $checkStmt->close();

    header("Location: mystores.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Árazás</title>
    <link rel="stylesheet" href="css/boti.css">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>
    <?php require "required/nav.php"; ?>
    <div class="pricing-container">
        <?php 
        $stmt = "SELECT * FROM pricing ORDER BY tier ASC";
        $result = $conn->query($stmt);
        
        while ($plan = $result->fetch_assoc()) { 
            $formattedPrice = number_format($plan['price'], 0, '', ' ');
            
            if (is_null($plan['store_count'])) {
                $storeText = "✓ <strong>Korlátlan bolt</strong> hozzáadása";
            } else if ($plan['store_count'] == 1) {
                $storeText = "✓ Maximum <strong>1 bolt</strong> kezelése";
            } else {
                $storeText = "✓ Maximum <strong>{$plan['store_count']} bolt</strong> kezelése";
            }

            if (is_null($plan['milestone_limit'])) {
                $milestoneText = "✓ Korlátlan mérföldkő beállítás";
            } else {
                $milestoneText = "✓ Legfeljebb <strong>{$plan['milestone_limit']} aktív mérföldkő</strong>";
            }

            $isFeatured = ($plan['tier'] == 2) ? ' featured' : '';
        ?>
            <form method="post" class="pricing-card<?= $isFeatured ?>">
                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">

                <p class="plan-name"><?= htmlspecialchars($plan['name']) ?></p>
                <p class="plan-price"><?= $formattedPrice ?> Ft <span style="font-size: 14px; color: var(--gray);">/ hó</span></p>
                <ul class="plan-features">
                    <li><?= $storeText ?></li>
                    <li>✓ Egyedi QR kódos pontgyűjtés</li>
                    
                    <?php if ($plan['allow_shared_db'] == 1): ?>
                        <li>✓ <strong>Közös adatbázis opció</strong> (üzletek közötti pontbeváltás)</li>
                    <?php else: ?>
                        <li>✓ Független adatbázis boltonként</li>
                    <?php endif; ?>

                    <li><?= $milestoneText ?></li>

                    <?php if ($plan['allow_events'] == 1): ?>
                        <li>✓ <strong>Események kezelése</strong> (pl. dupla pontok)</li>
                    <?php endif; ?>

                    <li>✓ Egyedi üzenetek a vásárlóknak és pultosoknak</li>

                    <?php if ($plan['tier'] >= 2): ?>
                        <li>✓ Ponttörténet és statisztikák</li>
                    <?php endif; ?>

                    <?php if ($plan['allow_export'] == 1): ?>
                        <li>✓ Részletes adatexportálás (CSV / Excel)</li>
                    <?php endif; ?>

                    <?php if ($plan['allow_priority_support'] == 1): ?>
                        <li>✓ Kiemelt ügyféltámogatás</li>
                    <?php endif; ?>
                </ul>
                <input type="submit" name="select_plan_btn" value="Kiválasztás" class="btn-link" style="border: none; cursor: pointer;">
            </form>
        <?php } ?>
    </div>

</body>
</html>