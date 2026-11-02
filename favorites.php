<?php

require "required/config.php";
require "required/functions.php";
if(!isset($_COOKIE['id'])) {
    header("Location: login.php");
    
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/nav.css">
    <title>Kedvenc Boltok</title>
</head>

<body>
    <header>
        <?php require "required/nav.php"; ?>
    </header>
    <?php
        $lekerdezes = "SELECT storeid,userid from stores WHERE userid = '$_COOKIE[id]'";
        $talalt = $conn->query($lekerdezes);
        while($eredmeny = $talalt->fetch_assoc()) {
            echo "Store ID: " . $eredmeny['storeid'] . ", User ID: " . $eredmeny['userid'] . "<br>";
        }
    ?>
</body>
</html>