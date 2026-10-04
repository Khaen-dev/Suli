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
    <link rel ="stylesheet" href = "css/konrad.css">
    <title>Főoldal</title>
</head>
<body>
    <form class = 'search'>
    <input type="text" name = "search-bar" id="search-bar" placeholder = 'Keress egy boltot!'>
    <div id = "search-box"></div>

    </form>

</body>

<script>
    $("#search-box").load("indexsearch.php?keresett=");

document.getElementById("search-bar").addEventListener('keyup', (e)=>{
    var ertek = e.target.value;
    $("#search-box").load("indexsearch.php?keresett="+ertek);
});
</script>
</html>