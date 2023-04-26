/**
 * Fonction permettant d'aggrandir une card sur la page d'index
 *
 * @param element
 */
function scaleUp(element) {
    // On ajoute une rotation et un aggrandissement de la card
    element.style.transform = "scale(1.1) rotate(-7deg)";
    // On met l'index à un niveau élevé pour que la card qui est hover soit au dessu des autres
    element.style.zIndex = "9";
    // On ajoute une transition de 0.3s
    element.style.transition = "transform 0.3s, box-shadow 0.3s";
    // Et on ajoute une bordure en ombre sur la carte
    element.style.boxShadow = "0 6px 8px rgba(0, 0, 0, 0.2)";
}

/**
 * Fonction permettant de remettre une carte à sa position initiale
 *
 * @param element
 */
function scaleDown(element) {
    element.style.transform = "scale(1) rotate(0deg)";
    element.style.zIndex = "2";
    element.style.transition = "transform 0.3s, box-shadow 0.3s";
    element.style.boxShadow = "0 4px 6px rgba(0, 0, 0, 0.1)";
}

/**
 * Fonction permettant d'animer les flashcards
 *
 */
function animateFlashcards() {
    // On récupère toutes les flashcards grâce à toutes les divisions qui possèdent la classe .flashcard
    const flashcards = document.querySelectorAll('.flashcard');
    // Pour chaque (donc avec la boucle foreach) :
    flashcards.forEach((flashcard, index) => {
        // Lambda expression permettant d'ajouter un temps entre chaque animation de flashcard
        setTimeout(() => {
            // On enleve le caractère invisible de la carte
            flashcard.classList.remove('hidden');
            // Et on ajoute son effet de transition d'opacité et de déplacement
            flashcard.style.transition = "opacity 0.6s, transform 0.6s";
        }, 1400 + index * 200);
    });
}

document.addEventListener('DOMContentLoaded', () => {

    animateFlashcards();

    // On récupère le titre
    const titleElement = document.getElementById("animated-title");
    // Et on le décompose à chaque lettre
    const titleText = titleElement.textContent;
    let wrappedText = "";

    // Et donc pour chaque lettre :
    for (let i = 0; i < titleText.length; i++) {
        // Si le contenu est un espace, on ne le modifie pas,
        if (titleText[i] === " ") {
            wrappedText += titleText[i];
        }
        // Sinon,
        // On crée un nouveau texte spécial, possédant des lettres entourées de span
        else {
            wrappedText += `<span>${titleText[i]}</span>`;
        }
    }

    // Et donc on modifie le texte du titre par notre nouveau texte possédant des spans
    titleElement.innerHTML = wrappedText;
    const headerText = document.querySelector('.why-join h2');
    // On récupère toutes les lettres grâce aux délimitations de "span"
    const letters = headerText.querySelectorAll('span');
    // Et on ajoute une durée en ms entre chaque lettre
    const duration = 15;
    // Ainsi que le delai d'apparition du titre
    const delay = 1650;

    // Et donc, pour chaque lettre, on ajoute leur animation respective
    letters.forEach((letter, index) => {
        // En n'oubliant pas d'ajouter la duration entre chaque lettre pour que tout n'apparaisse pas d'un coup
        letter.style.animation = `titleAnimation 0.16s ${delay + duration * index}ms forwards`;
        letter.style.display = 'inline-block';
        letter.style.opacity = '0';
    });

});