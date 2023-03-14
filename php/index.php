<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <title>Twitturtle</title>
    <link rel="shortcut icon" href="./favicon.ico">
</head>

<body>
<div class = "Container">
    <div class = "Navigation">
        <a href = "index.php"><img src = "./images/logo_site.png" alt = "Logo" style = "width:5vw; height: 5vw; padding: 1.2vw; padding-bottom: 0;"></a>
        <ul>
            <li class = "NavigationButton"><a href = "index.php">Accueil</a></li>
            <li class = "NavigationButton">Explorer</li>
            <li class = "NavigationButton">Notifications</li>
            <li class = "NavigationButton">Messages</li>
            <li class = "NavigationButton"><a href = "profil.php">Profil</a></li>
            <li class = "NavigationButton"><a href = "connect.php">Se connecter</a></li>
        </ul>
        <div class = "center">
            <li class = "NavigationTweeter">Tweeter</li>
        </div>
    </div>

    <div class = "MainContainer">
        <h1>Accueil</h1>
        <div class = "NewMessage">
            <a href = "profil.php"><img class = "AvatarMessage" src = "./images/titan.png"></a>
            <textarea class = "Content" placeholder="Quoi de neuf ?" rows="1" maxlength="240"></textarea>
            <span class = "Border" style="width: 80%;"></span>
            <div class = "ButtonPosition">
                <a><button class = "Tweeter">Tweeter</button></a>
            </div>
            <p>HASHTAG</p>
            <p>EMPLACEMENT</p>
        </div>
        <div class = "TimeLine">
            <p>TIMELINE</p>
        </div>
    </div>

    <div class = "Trends">
        <h2>Tendances</h2>
        <p>1ER</p>
        <p>2EME</p>
        <p>3EME</p>
    </div>
</div>


</body>

</html>

