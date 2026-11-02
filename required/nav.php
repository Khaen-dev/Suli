<script src="https://kit.fontawesome.com/7a9ed78c85.js" crossorigin="anonymous"></script>
<div class="nav">
    <ul>
        <li><a href="index.php" class="menu-left">Főoldal</a></li>
        <li><a href="addstore.php" class="menu-left">Bolt hozzáadása</a></li>
        <?php
            $inquiry = "SELECT userid FROM user_subscriptions WHERE userid = $_COOKIE[id]";
            $found = $conn->query($inquiry);
            if(mysqli_num_rows($found) == 1){
        ?>
            <li><a href="mystores.php" class="menu-left">Saját boltok</a></li>
        <?php } ?>
        <div class="dropdown menu-right">
            <button class="dropbtn"><i class="fa-solid fa-bars"></i></button>
            <div class="dropdown-content">
                <a href="favorites.php">Kedvenc boltok</a>
                <a href="profile.php">Profilom</a>
                <?php if(isset($_COOKIE["id"])){ ?>
                    <a href="logout.php">Kijelentkezés</a>
                <?php } else { ?>
                    <a href="login.php">Bejelentkezés</a>
                    <a href="register.php">Regisztráció</a>
                <?php } ?>
            </div>
        </div>
    </ul>
</div>