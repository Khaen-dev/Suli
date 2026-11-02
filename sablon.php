<?php

require "required/functions.php";
require "required/config.php";
    $name = $_GET['name'];


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel = "stylesheet" href ="css/nav.css">
    <title>Document</title>
</head>
<body>
    <header>
        <?php require "required/nav.php"; ?>
    </header>
    <?php
    
    $lekerdezes = "SELECT * FROM stores WHERE name = '$name'";
    $talalt = $conn->query($lekerdezes);
    $eredmeny = $talalt->fetch_assoc();
    echo "<h1>" . $eredmeny['name'] . "</h1>";
    echo "<p>" . $eredmeny['address'] . "</p>";
    if($eredmeny['contact_name'] != null) {
        echo "<p>Kapcsolattartó: " . $eredmeny['contact_name'] . "</p>";
    }
    if($eredmeny['contact_email'] != null) {
        echo "<p>Email: " . $eredmeny['contact_email'] . "</p>";
    }
    if(isset($_POST['kedvenc'])) {
        $lekerdezes = "SELECT storeid,userid from favorites WHERE storeid = $eredmeny[id] AND userid = $_COOKIE[id]";
        $talalt = $conn->query($lekerdezes);
        if(mysqli_num_rows($talalt) == 1) {
            echo "<p>A bolt már szerepel a kedvencek között!</p>";
        } else {
            $storeId = $eredmeny['id'];
            $userId = $_COOKIE['id'];
            $conn->query("INSERT INTO favorites (id,storeid, userid) VALUES (id,'$storeId', '$userId')");
            echo "<p>A bolt hozzáadva a kedvencekhez!</p>";
        } 
    }
    
        ?>
        <form method ="post">
            <input type="submit" id = "kedvenc" name="kedvenc" value="Kedvencekhez adás" onclick="favorite()">
        </form> 
</body>
</html>
<script>

    function favorite(){
        document.getElementById("kedvenc").style.display = "none";
    }
</script>