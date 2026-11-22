<?php
    require "required/config.php";
    require "required/functions.php";

    
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
        if(isset($_POST['ln-change'])){
            ?>
            <script>
                document.getElementById("last-name").style.display = "none";
                document.getElementById("lastname-txt").style.display = "block";
            </script>
            <?php
        }
        $inquiry = "SELECT * FROM users WHERE id=$_COOKIE[id]";
        $found = $conn->query($inquiry);
        $user = $found->fetch_assoc();
        echo "<h3>Üdv, $user[last_name] $user[first_name]!</h3>";
        echo "<form method='post'>";
        echo "<label class='profile-info'>Vezetéknév:"."<input type='text' id='lastname-txt' name='new-ln' placeholder='$user[last_name]'>"."<input type='submit' name='ln-change' value='Módosítás'> <span id='last-name'>$user[last_name]</span></label>";
        echo "<label class='profile-info'>Keresztnév:"."<input type='text' name='new-fn' placeholder='$user[first_name]'>"."<input type='submit' name='fn-change' value='Módosítás'> <span id='firstname'>$user[first_name]</span></label>";
        echo "<label class='profile-info'>Jelszó:"."<input type='text' name='new-pw' placeholder='****'>"."<input type='submit' name='pw-change' value='Módosítás'> <span id='password'>*****</span></label>";
        echo "<label class='profile-info'>Email:"."<input type='text' name='new-email' placeholder='$user[email]'>"."<input type='submit' name='email-change' value='Módosítás'> <span id='email'>$user[email]</span></label>";
        echo "</form>";
        ?>
        </div>
    </div>
</body>
</html>