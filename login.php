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
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <form method="post" class="login-form">
        <label class="header">Bejelentkezés</label>
        <label for="email" class="title">Email</label>
        <input type="email" name="email" id="email" placeholder="Email@email.com">
        <label for="password" class="title">Jelszó</label>
        <input type="password" name="password" id="password" placeholder="Jelszó">
        <input type="submit" name="login-btn" value="Bejelentkezés">
    </form>
</body>
</html>