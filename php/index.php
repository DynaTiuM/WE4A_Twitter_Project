<!DOCTYPE html>

<html lang = "fr">

<head>
    <meta charset = "utf-8">
    <link rel = "stylesheet" href = "./css/stylesheet.css">
    <title>Twitturtle</title>
</head>

<body>
<div class = "Container">
    <div class = "Navigation">
        <img src = "./images/pet-house.png" alt = "Logo" style = "width:2.2vw; height: 2.2vw; padding: 1.2vw; padding-bottom: 0;">
        <ul>
            <li class = "NavigationButton"><a href = "index.php">Accueil</a></li>
            <li class = "NavigationButton">Explorer</li>
            <li class = "NavigationButton">Notifications</li>
            <li class = "NavigationButton">Messages</li>
            <li class = "NavigationButton"><a href = "profil.php">Profil</a></li>
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

