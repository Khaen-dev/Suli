<?php 

require "required/config.php";

if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    die();
}

$userId = $_COOKIE['id'];
$storeId = $_GET['id'] ?? null;

if (!$storeId) {
    header("Location: mystores.php");
    exit();
}

$storeStmt = $conn->prepare("SELECT * FROM stores WHERE id = ? AND userid = ?");
$storeStmt->bind_param("ii", $storeId, $userId);
$storeStmt->execute();
$storeResult = $storeStmt->get_result();

if ($storeResult->num_rows === 0) {
    header("Location: mystores.php");
    exit();
}
$store = $storeResult->fetch_assoc();
$storeStmt->close();

$subStmt = $conn->prepare("
    SELECT subs.*, pricing.* 
    FROM user_subscriptions AS subs
    JOIN pricing ON subs.subscription = pricing.id 
    WHERE subs.userid = ?
");
$subStmt->bind_param("i", $userId);
$subStmt->execute();
$subResult = $subStmt->get_result();
$sub = $subResult->fetch_assoc();
$subStmt->close();

$expired = false;
if ($sub && strtotime($sub['expires']) < time()) {
    $expired = true;
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($store['name']) ?> - Vezérlőpult</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/086e7cefb3.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/boti.css">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>
    <?php require "required/nav.php"; ?>

    <div class="dashboard-wrapper">
        <div class="dashboard-sidebar">
            <div class="store-info-box">
                <h2><?= htmlspecialchars($store['name']) ?></h2>
                <p><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($store['type']) ?></p>
            </div>

            <div class="sidebar-nav">
                <button type="button" class="active" onclick="switchTab('overview', this)">
                    <i class="fa-solid fa-house"></i> Áttekintés
                </button>
                <button type="button" onclick="switchTab('qr', this)">
                    <i class="fa-solid fa-qrcode"></i> QR Kód & Pontok
                </button>
                <button type="button" onclick="switchTab('milestones', this)">
                    <i class="fa-solid fa-flag-checkered"></i> Mérföldkövek
                </button>
                <button type="button" onclick="switchTab('events', this)">
                    <i class="fa-solid fa-calendar-days"></i> Események
                </button>
                <button type="button" onclick="switchTab('messages', this)">
                    <i class="fa-solid fa-message"></i> Egyedi Üzenetek
                </button>
            </div>

            <a href="mystores.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Vissza a boltokhoz</a>
        </div>

        <div class="dashboard-content">

            <?php if ($expired): ?>
                <div class="alert-box">
                    Az előfizetésed lejárt! Bizonyos funkciók korlátozva lehetnek. <a href="pricing.php" style="color: var(--black); text-decoration: underline;">Megújítás itt</a>.
                </div>
            <?php endif; ?>

            <div id="section-overview" class="content-section active">
                <h2>Áttekintés</h2>
                <p>Üdvözlünk a(z) <strong><?= htmlspecialchars($store['name']) ?></strong> vezérlőpultján! Itt áttekintheted a bolt alapvető adatait és gyorselérést kapsz a funkciókhoz.</p>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; color: var(--dark-gray);">
                    <li><strong>Cím:</strong> <?= htmlspecialchars($store['address']) ?></li>
                    <li><strong>Kapcsolattartó:</strong> <?= htmlspecialchars($store['contact_name']) ?> (<?= htmlspecialchars($store['contact_email']) ?>)</li>
                    <li><strong>Telefonszám:</strong> <?= htmlspecialchars($store['tel']) ?></li>
                </ul>
            </div>

            <div id="section-qr" class="content-section">
                <h2>QR Kód & Pontgyűjtés</h2>
                <p>Még kitaláljuk.</p>
                <!-- <div style="padding: 40px; background: #f8f9fb; border: 2px dashed #ccc; border-radius: 15px; text-align: center; color: var(--gray);">
                    [QR Kód Helye]
                </div> -->
            </div>

            <div id="section-milestones" class="content-section">
                <h2>Mérföldkövek kezelése</h2>
                <p>
                    <?php if (is_null($sub['milestone_limit'])): ?>
                        Jelenlegi csomagodban <strong>korlátlan</strong> mérföldkő beállítás engedélyezett.
                    <?php else: ?>
                        Jelenlegi csomagod limitje: legfeljebb <strong><?= $sub['milestone_limit'] ?> aktív mérföldkő</strong>.
                    <?php endif; ?>
                </p>
                <button type="button" class="dashboard-btn" style="background-color: var(--default); color: var(--white); padding: 10px 15px; border: none; border-radius: 10px; font-weight: bold; cursor: pointer;">Új mérföldkő hozzáadása</button>
            </div>

            <div id="section-events" class="content-section">
                <h2>Események & Akciók</h2>
                <?php if ($sub && $sub['allow_events'] == 1): ?>
                    <p>Itt állíthatsz be időszakos akciókat (pl. dupla pontok napjai).</p>
                    <button type="button" style="background-color: var(--default); color: var(--white); padding: 10px 15px; border: none; border-radius: 10px; font-weight: bold; cursor: pointer;">Új esemény létrehozása</button>
                <?php else: ?>
                    <p style="color: var(--red);">Ez a funkció a te csomagodban nem érhető el. Kérlek frissítsd az előfizetésedet a Haladó vagy Üzleti csomagra az események kezeléséhez!</p>
                    <a href="pricing.php" class="back-btn" style="display:inline-block; background-color: var(--default);">Csomag frissítése</a>
                <?php endif; ?>
            </div>

            <div id="section-messages" class="content-section">
                <h2>Egyedi Üzenetek</h2>
                <p>Testreszabott üzenetek beállítása, amelyek megjelennek a pultosnak és a vásárlónak pontgyűjtéskor.</p>
            </div>

        </div>
    </div>

    <script>
        function switchTab(tabId, buttonElement) {
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });

            document.querySelectorAll('.sidebar-nav button').forEach(btn => {
                btn.classList.remove('active');
            });

            document.getElementById('section-' + tabId).classList.add('active');
            buttonElement.classList.add('active');
        }
    </script>
</body>
</html>