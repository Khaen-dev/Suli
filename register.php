<?php
    require "required/config.php";
    require "required/functions.php";

    if(isset($_POST['reg-btn'])){

        $user = findUser($_POST['email']);
        if($user == 0){

            if($_POST['pass1'] == $_POST['pass2']){

                $hash = password_hash($_POST['pass1'], PASSWORD_DEFAULT);
                $conn->query("INSERT INTO users VALUES(id, '$_POST[email]', '$_POST[last_name]', '$_POST[first_name]', '$hash')");

                header("Location: login.php");
            }
            else{
                alert("Nem egyeznek a jelszavak!");
            }
        }
        else{
            alert("Van már ilyen felhasználó!");
        }
    }
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/styles.css">
    <title>Regisztráció</title>
</head>
<body>
    <header> <?php require "required/nav.php"; ?></header>
    <form method="post" class="login-form">
        <label class="header">Regisztráció</label>
        <label class="title">Email</label>
        <input type="email" name="email" placeholder="valami@vmi.com">
        <label class="title">Vezetéknév</label>
        <input type="text" name="last_name" placeholder="Fazekas">
        <label class="title">Keresztnév</label>
        <input type="text" name="first_name" placeholder="Jenő">
        <label class="title">Jelszó</label>
        <input type="password" name="pass1" placeholder="*****">
        <label class="title">Jelszó megerősítése</label>
        <input type="password" name="pass2" placeholder="*****">
        <input type="submit" name="reg-btn" value="Regisztráció">
        </form>
</body>
</html>