<?php

require "required/functions.php";
require "required/config.php";


?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel = "stylesheet" href ="css/konrad.css">
    <title>Index search</title>
</head>
<body>  
    <?php
    
    $stmt = "SELECT img, name, place FROM boltok WHERE name LIKE '%$_GET[keresett]%' OR place LIKE '%$_GET[keresett]%'";
    $found = $conn->query($stmt);
    while($f = $found->fetch_assoc()){
        echo "<a class='talalat' href='sablon.php?name=$f[name]'>" . $f['place'] . "</a>";

    }
    ?>
    </form>

</body>
</html>