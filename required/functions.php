<?php
    $conn = new mysqli("localhost", "root", "", "2evi_proba");

    function findUser($email){

        global $conn;
        $found_user = $conn->query("SELECT email, password FROM users WHERE email = '$email'");

        if(mysqli_num_rows($found_user) == 0){
            return 0;
        }
        else{
            $user = $found_user->fetch_assoc();
            return $user;
        }
    }

    function alert($text){
        echo "<script>alert('$text')</script>";
    }
?>