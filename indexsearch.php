<?php

require "/required/functions.php";
require "/required/config.php";


?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>  
    <?php
    
    $stmt = "SELECT * FROM boltok WHERE name LIKE '%$_GET[keresett]%'";
    $found = $conn->query($stmt);
    while($f = $found->fetch_assoc()){
    echo "<img src = '$f[img]'></img>"." | ".$f['name']." | ".$f['place'];
    ?>
    
    <?php } ?>
</body>
</html>