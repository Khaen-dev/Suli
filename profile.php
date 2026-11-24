<?php
    require "required/config.php";
    require "required/functions.php";

    if(isset($_POST['save-changes'])){
        $inquiry = "SELECT * FROM users WHERE id=$_COOKIE[id]";
        $found = $conn->query($inquiry);
        $user = $found->fetch_assoc();

        if(isset($_POST['new_ln'])){
            $conn->query("UPDATE users SET last_name = '$_POST[new_ln]' WHERE id=$_COOKIE[id]");
        }
        if(isset($_POST['new_fn'])){
            $conn->query("UPDATE users SET first_name = '$_POST[new_fn]' WHERE id=$_COOKIE[id]");
        }
        if(isset($_POST['new_pw'])){

            if(password_verify($_POST['old_pw'], $user['password'])){
                $hash = password_hash($_POST['new_pw'], PASSWORD_DEFAULT);
                $conn->query("UPDATE users SET first_name = '$hash' WHERE id=$_COOKIE[id]");
            }
        }
        if(isset($_POST['new_email'])){
            $conn->query("UPDATE users SET email = '$_POST[new_email]' WHERE id=$_COOKIE[id]");
        }
    }
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/nav.css">
    <title>Profilom</title>

    <!-- REFRESHER -->
    <!-- <meta http-equiv="refresh" content="2"> -->
</head>
<body>
    <header> <?php require "required/nav.php"; ?></header>
    <!-- név módosítás (label-el megjelenites, ranyom a modositas gombra es JS a helyere egy atirhato form-ot rak)
         jelszó módosítás
         email módosítás
    -->
    <div class="profile">
        <div class="profile-card">
        <?php
        
        $inquiry = "SELECT * FROM users WHERE id=$_COOKIE[id]";
        $found = $conn->query($inquiry);
        $user = $found->fetch_assoc();
        ?>
        <h3>Üdv, <?= $user['last_name']; ?> <?= $user['first_name']; ?>!</h3>
        <form method='post'>
            <label class='profile-info'>Vezetéknév: 
                <input type='text' id='lastname-txt' name='new_ln' placeholder='<?= $user['last_name']; ?>' value="<?= $user['last_name']; ?>">
                <a onclick="showForm('lastname')" id='lastname'><?= $user['last_name']; ?></a>
            </label>
            <label class='profile-info'>Keresztnév: 
                <input type='text' name='new_fn' id='firstname-txt' placeholder='<?= $user['first_name']; ?>' value="<?= $user['first_name']; ?>">
                <a onclick="showForm('firstname')" id='firstname'><?= $user['first_name']; ?></a>
            </label>
            <label class='profile-info'>Jelszó:
                <input type='password' name="new_pw" placeholder="Új jelszó" id="password-txt2">
                <input type='password' name='old_pw' placeholder='Régi jelszó' id="password-txt">
                <a onclick="showForm('password')"id='password'>*****</a>
            </label>
            <label class='profile-info'>Email:
                <input type='text' name='new_email' id="email-txt" placeholder='<?= $user['email']; ?>' value="<?= $user['email']; ?>">
                <a onclick="showForm('email')" id='email'><?= $user['email']; ?></a>
            </label>
            <input type="submit" name="save-changes" value="Változtatások mentése" class="savechanges-btn">
        </form>
        
        </div>
    </div>
</body>
</html>
<script>
    function showForm(form){
        if(form == "lastname"){
            document.getElementById("lastname-txt").style.display = "inline-block";
            document.getElementById("lastname").style.display = "none";
        }
        else if(form == "firstname"){
            document.getElementById("firstname-txt").style.display = "inline-block";
            document.getElementById("firstname").style.display = "none";
        }
        else if(form == "password"){
            document.getElementById("password-txt").style.display = "inline-block";
            document.getElementById("password-txt2").style.display = "block";
            document.getElementById("password").style.display = "none";
        }
        else if(form == "email"){
            document.getElementById("email-txt").style.display = "inline-block";
            document.getElementById("email").style.display = "none";
        }
    }
</script>