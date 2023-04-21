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