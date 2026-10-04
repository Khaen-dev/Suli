<?php 

require "required/config.php";

// setcookie("id", 1, time()+3600, "/");

if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    die();
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boltjaim</title>
</head>
<body>
    <?php 
    $stmt = "SELECT * FROM stores WHERE userid = $_COOKIE[id]";
    $result = $conn->query($stmt);
    while ($store = $result->fetch_assoc()) { ?>
        <p><?= $store['name']; ?></p>
        <p><?= $store['type']; ?></p>
        <p><?= $store['contact_name']; ?></p>
    <?php } ?>
</body>
</html>