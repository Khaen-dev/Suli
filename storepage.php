<?php

require "required/functions.php";
require "required/config.php";
    $name = $_GET['name'];


?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/konrad.css">
    <link rel = "stylesheet" href ="css/nav.css">
    <link rel = "stylesheet" href ="css/sablon.css">
    <title>Bolt részletei</title>
</head>
<body>
    <header>
        <?php require "required/nav.php"; ?>
    </header>
    <main class="store-page">
    <?php

    $lekerdezes = "SELECT * FROM stores WHERE name = '$name'";
    $talalt = $conn->query($lekerdezes);
    $eredmeny = $talalt->fetch_assoc();

    if(!$eredmeny) {
        echo "<div class='store-card'><h1>Nem található</h1><div class='message error'>A keresett bolt nem létezik.</div></div>";
    } else {
    ?>
    <section class="store-card">
        <h1><?php echo htmlspecialchars($eredmeny['name']); ?></h1>
        <p class="store-address"><?php echo htmlspecialchars($eredmeny['address']); ?></p>

        <ul class="store-info">
            <?php if($eredmeny['contact_name'] != null) { ?>
                <li><span class="label">Kapcsolattartó</span><span><?php echo htmlspecialchars($eredmeny['contact_name']); ?></span></li>
            <?php } ?>
            <?php if($eredmeny['contact_email'] != null) { ?>
                <li><span class="label">Email</span><a href="mailto:<?php echo htmlspecialchars($eredmeny['contact_email']); ?>"><?php echo htmlspecialchars($eredmeny['contact_email']); ?></a></li>
            <?php } ?>
        </ul>

    <?php
    if(isset($_COOKIE['id'])) {
        $storeId = $eredmeny['id'];
        $userId = $_COOKIE['id'];

        if(isset($_POST['kedvenc'])) {
            $lekerdezes = "SELECT storeid,userid from favorites WHERE storeid = $storeId AND userid = $userId";
            $talalt = $conn->query($lekerdezes);
            if(mysqli_num_rows($talalt) == 1) {
                echo "<div class='message info'>A bolt már szerepel a kedvencek között!</div>";
            } else {
                $conn->query("INSERT INTO favorites (id,storeid, userid) VALUES (id,'$storeId', '$userId')");
                echo "<div class='message success'>A bolt hozzáadva a kedvencekhez!</div>";
            }
        }

        if(isset($_POST['visszavon'])) {
            $conn->query("DELETE FROM favorites WHERE storeid = $storeId AND userid = $userId");
            echo "<div class='message success'>A bolt eltávolítva a kedvencek közül!</div>";
        }

        $talalt = $conn->query("SELECT storeid FROM favorites WHERE storeid = $storeId AND userid = $userId");
        $kedvenc = mysqli_num_rows($talalt) > 0;
    ?>
        <form method="post" class="store-actions">
            <?php if($kedvenc) { ?>
                <input type="submit" name="visszavon" class="btn-favorite btn-remove" value="Eltávolítás a kedvencek közül">
            <?php } else { ?>
                <input type="submit" name="kedvenc" class="btn-favorite" value="Kedvencekhez adás">
            <?php } ?>
        </form>
    <?php } ?>
    </section>
    <?php } ?>
    </main>
</body>
</html>
