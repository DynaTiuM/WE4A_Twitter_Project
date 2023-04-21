<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twitturtle</title>
    <link rel="stylesheet" href="../css/home.css">

    <script src = "../js/indexAnimations.js"></script>
</head>
<body>


<span class = "image"></span>
<div class="container">
    <header>
        <img src="../images/logo.png" alt="Logo Twitturtle" class="logo">
    </header>
    <section class="hero">
        <div class="title-container">
            <h1>Bienvenue sur Twitturtle</h1>
        </div>
        <div class = "text-container">
            <p class = "under_title">Rejoignez notre communauté d'amoureux des animaux !</p>
        </div>

        <form action = "./connect.php">
            <button class="button" type = "submit">Rejoindre</button>
        </form>
    </section>

    <section class="why-join">
        <div class="text-container">
            <h2 id="animated-title">Pourquoi rejoindre notre réseau social ?</h2>
        </div>
        <div class="flashcards">
            <div class="flashcard flashcard hidden" style="--delay: 0" onmouseover="scaleUp(this)" onmouseout="scaleDown(this)">
                <h3 style = " color : #9381FF">Échanges et conseils</h3>
                <p>Partagez vos expériences avec d'autres amoureux des animaux, demandez conseils sur la santé et l'éducation de vos compagnons.</p>
            </div>
            <div class="flashcard flashcard hidden" style="--delay: 1" onmouseover="scaleUp(this)" onmouseout="scaleDown(this)">
                <h3 style = "color : #FF5733">Adoption responsable</h3>
                <p>Découvrez des animaux à adopter et soutenez les organisations de protection des animaux en offrant un foyer aimant.</p>
            </div>
            <div class="flashcard flashcard hidden" style="--delay: 2" onmouseover="scaleUp(this)" onmouseout="scaleDown(this)">
                <h3 style = "color : #F9CB40 ">Réseau animalier</h3>
                <p>Connectez-vous avec d'autres passionnés, échangez idées et expériences pour enrichir votre vie et celle de vos animaux.</p>
            </div>

        </div>
    </section>
</div>


</body>
</html>