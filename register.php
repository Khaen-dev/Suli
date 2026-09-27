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
    <title>Regisztráció</title>
</head>
<body>
    <form method="post">
        <label>Regisztráció</label>
        <input type="email" name="email" placeholder="email@email.com">
        <input type="text" name="last_name" placeholder="Vezetéknév">
        <input type="text" name="first_name" placeholder="Keresztnév">
        <input type="password" name="pass1" placeholder="Jelszó">
        <input type="password" name="pass2" placeholder="Jelszó megerősítése">
        <input type="submit" name="reg-btn" value="Regisztráció">
        </form>
</body>
</html>