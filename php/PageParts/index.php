<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twitturtle</title>
    <link rel="stylesheet" href="../css/home.css">


    <script>
        function scaleUp(element) {
            element.style.transform = "scale(1.1) rotate(-7deg)";
            element.style.zIndex = "9";
            element.style.transition = "transform 0.3s, box-shadow 0.3s";
            element.style.boxShadow = "0 6px 8px rgba(0, 0, 0, 0.2)";
        }

        function scaleDown(element) {
            element.style.transform = "scale(1) rotate(0deg)";
            element.style.zIndex = "2";
            element.style.transition = "transform 0.3s, box-shadow 0.3s";
            element.style.boxShadow = "0 4px 6px rgba(0, 0, 0, 0.1)";
        }

        function animateFlashcards() {
            const flashcards = document.querySelectorAll('.flashcard');
            flashcards.forEach((flashcard, index) => {
                setTimeout(() => {
                    flashcard.classList.remove('hidden');
                    flashcard.style.transition = "opacity 0.6s, transform 0.6s";
                }, 1400 + index * 200);
            });
        }


        document.addEventListener('DOMContentLoaded', () => {

            animateFlashcards();

            const titleElement = document.getElementById("animated-title");
            const titleText = titleElement.textContent;
            let wrappedText = "";

            for (let i = 0; i < titleText.length; i++) {
                if (titleText[i] === " ") {
                    wrappedText += titleText[i];
                } else {
                    wrappedText += `<span>${titleText[i]}</span>`;
                }
            }

            titleElement.innerHTML = wrappedText;
            const headerText = document.querySelector('.why-join h2');
            const letters = headerText.querySelectorAll('span');
            const duration = 15; // Durée en ms entre chaque lettre
            const delay = 1650;

            letters.forEach((letter, index) => {
                letter.style.animation = `titleAnimation 0.16s ${delay + duration * index}ms forwards`;
                letter.style.display = 'inline-block';
                letter.style.opacity = '0';
            });

        });
    </script>


</head>
<body>


<span class = "image"></span>
<div class="container">
    <header>
        <img src="../images/logo_white.png" alt="Logo Twitturtle" class="logo">
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