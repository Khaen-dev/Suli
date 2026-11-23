<?php

require "required/config.php";
require "required/favo.php";

$userId = getLoggedInUserId();
$storeId = (int)($_GET['id'] ?? 0);

$store = queryOne($conn, "SELECT * FROM stores WHERE id = ?", [$storeId]);
if ($store === null) {
    header("Location: mystores.php");
    exit();
}

$role = getUserRole($conn, $store, $userId);
if ($role === null) {
    header("Location: mystores.php");
    exit();
}

$subscription = getSubscription($conn, $store['owner_userid']);
$isActive = $subscription['is_active'];
$pointOwner = getPointOwner($store);

$tabs = [
    'overview'   => ['title' => 'Áttekintés',      'icon' => 'fa-house',           'permission' => null],
    'customers'  => ['title' => 'Vásárlók',        'icon' => 'fa-users',           'permission' => 'points'],
    'milestones' => ['title' => 'Mérföldkövek',    'icon' => 'fa-flag-checkered',  'permission' => 'milestones'],
    'events'     => ['title' => 'Események',       'icon' => 'fa-calendar-days',   'permission' => 'events'],
    'messages'   => ['title' => 'Egyedi üzenetek', 'icon' => 'fa-message',         'permission' => 'messages'],
    'staff'      => ['title' => 'Munkatársak',     'icon' => 'fa-user-gear',       'permission' => 'staff'],
    'settings'   => ['title' => 'Beállítások',     'icon' => 'fa-gear',            'permission' => 'settings'],
];

$activeTab = $_GET['tab'] ?? 'overview';
if (!isset($tabs[$activeTab])) {
    $activeTab = 'overview';
}
$requiredPermission = $tabs[$activeTab]['permission'];
if ($requiredPermission !== null && !hasPermission($role, $requiredPermission)) {
    $activeTab = 'overview';
}

$tabUrl = "dashboard.php?id=" . $storeId . "&tab=" . $activeTab;
$message = "";
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $store['name'] ?> - Vezérlőpult</title>
    <script src="https://kit.fontawesome.com/086e7cefb3.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/boti.css">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>
    <?php require "required/nav.php"; ?>

    <div class="dashboard-wrapper">
        <div class="dashboard-sidebar">
            <div class="store-info-box">
                <h2><?= $store['name'] ?></h2>
                <p><i class="fa-solid fa-tag"></i> <?= $store['type'] ?></p>
                <p><i class="fa-solid fa-user"></i> <?= ROLE_NAMES[$role] ?></p>
            </div>

            <div class="sidebar-nav">
                <?php foreach ($tabs as $tabKey => $tab) { ?>
                    <?php if ($tab['permission'] === null || hasPermission($role, $tab['permission'])) { ?>
                        <a href="dashboard.php?id=<?= $storeId ?>&tab=<?= $tabKey ?>" class="<?= $tabKey === $activeTab ? 'active' : '' ?>">
                            <i class="fa-solid <?= $tab['icon'] ?>"></i> <?= $tab['title'] ?>
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>

            <a href="mystores.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Vissza a boltokhoz</a>
        </div>

        <div class="dashboard-content">
            <?php if (!$isActive) { ?>
                <div class="alert-box">
                    Nincs érvényes előfizetés, a bolt csak olvasható.
                    <a href="pricing.php">Előfizetés</a>
                </div>
            <?php } ?>

            <div class="content-section">
                <?php require "dashboard_tabs/" . $activeTab . ".php"; ?>
            </div>
        </div>
    </div>
</body>
</html>
