<?php
    require "required/config.php";
    require "required/functions.php";

    if(isset($_POST['login-btn'])){

        $user = findUser($_POST['email']);
        if($user != 0){

            if(password_verify($_POST['password'], $user['password'])){
                setcookie("id", $user['id'], time()+3600, "/");
                header("Location: index.php");
            }
            else{
                alert("Helytelen jelszó!");
            }
        }
        else{
            alert("Helytelen email!");
        }
    }
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
</head>
<body>
    <form method="post">
        <label>Bejelentkezés</label>
        <input type="email" name="email" placeholder="Email@email.com">
        <input type="password" name="password" placeholder="Jelszó">
        <input type="submit" name="login-btn" value="Bejelentkezés">
    </form>
</body>
</html>