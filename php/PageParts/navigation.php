<?php
$loginStatus = CheckLogin();
?>

<div class = "Navigation">
    <a href = "index.php"><img src = "./images/logo_site.png" alt = "Logo" style = "width:5vw; height: 5vw; padding: 1.2vw; padding-bottom: 0;"></a>
    <ul>
        <li class = "NavigationButton"><a href = "index.php">Accueil</a></li>
        <li class = "NavigationButton">Explorer</li>
        <?php if($loginStatus[0]) { ?>
        <li class = "NavigationButton">Notifications</li>
        <li class = "NavigationButton">Messages</li>
        <li class = "NavigationButton"><a href = "profil.php">Profil</a></li>
        <?php
        } else {
        ?>
        <li class = "NavigationButton"><a href = "connect.php">Se connecter</a></li>
        <?php
        }
        ?>
    </ul>
    <?php if($loginStatus[0]) { ?>
    <div class = "center">
        <li class = "NavigationTweeter">Message</li>
    </div>
        <?php
    }
    ?>
</div>