<?php
$loginStatus = CheckLogin();
?>

<div class = "navigation">
    <a href = "index.php"><img src = "./images/logo_site.png" alt = "Logo" style = "width:5vw; height: 5vw; padding: 1.2vw; padding-bottom: 0;"></a>
    <ul>
        <li class = "menu-item"><a href = "index.php">Accueil</a></li>
        <li class = "menu-item">Explorer</li>
        <?php if($loginStatus[0]) { ?>
        <li class = "menu-item">Notifications</li>
        <li class = "menu-item">Messages</li>
        <li class = "menu-item"><a href = "profil.php?username=<?php echo urlencode($_COOKIE['username']); ?>">Profil</a></li>
        <?php
        } else {
        ?>
        <li class = "menu-item"><a href = "connect.php">Se connecter</a></li>
        <?php
        }
        ?>
    </ul>
    <?php if($loginStatus[0]) { ?>
    <div class = "center">
        <li class = "tweet-button">Message</li>
    </div>
        <?php
    }
    ?>
</div>