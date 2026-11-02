<?php

require "required/config.php";
require "required/functions.php";


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
        $lekerdezes = "SELECT name from stores WHERE favorite = 1";
        $talalt = $conn->query($lekerdezes);
        while($eredmeny = $talalt->fetch_assoc()) {
            echo $eredmeny['name'] . "<br>";
        }
    ?>
</body>
</html>