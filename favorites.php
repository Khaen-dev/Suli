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
    <link rel="stylesheet" href="css/favorites.css">
    <title>Kedvenc Boltok</title>
</head>

<body>
    <header>
        <?php require "required/nav.php"; ?>
    </header>
    <main>
    <h1>Kedvenc Boltok</h1>
    <?php

        $lekerdezes = "SELECT stores.name, stores.address FROM stores INNER JOIN favorites ON stores.id = favorites.storeid WHERE favorites.userid = '$_COOKIE[id]'";
        $talalt = $conn->query($lekerdezes);
        while($eredmeny = $talalt->fetch_assoc()) {
            echo "<div class='store'>";
            echo "<p><strong>" . $eredmeny['name'] . "</strong></p>";
            echo "<p>" . $eredmeny['address'] . "</p>";
            echo "</div>";
        }
    ?>
    </main>
</body>
</html>