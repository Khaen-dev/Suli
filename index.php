<?php

require "required/functions.php";
require "required/config.php";

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <link rel="stylesheet" href="css/domstyle.css">
    <title>Főoldal</title>
</head>
<body>
    <header>
        <?php require "required/nav.php"; ?>
    </header>
    <input type="text" name = "search-bar">
    <div id = "search-box"></div>
</body>
</html>
<script>
    $("#search-box").load("search-bar?keresett=");
    document.getElementById("search-bar").addEventListener('keyup', (e) =>{
        var ertek = e.target.value;
        $("#search-box").load("search-bar?keresett="+ertek);
    })
</script>