<?php
    $conn = new mysqli("localhost", "root", "", "2evi_proba");

    if($conn->connect_error){
        die("Sikertelen csatlakozás".$conn->connect_error);
    }
?>