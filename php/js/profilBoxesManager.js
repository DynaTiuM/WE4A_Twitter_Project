document.addEventListener("DOMContentLoaded", function() {
    // Récupération des boutons
    const messageBtn = document.getElementById("message-button");
    const answerBtn = document.getElementById("answer-button");
    const likeBtn = document.getElementById("like-button");

    // Récupération des contenus
    const messageContent = document.getElementById("message-content");
    const answerContent = document.getElementById("answer-content");
    const likeContent = document.getElementById("like-content");

    // Fonction qui désactive les boutons et affiche le contenu correspondant
    function switchContent(btn, content) {
        // On désactive tous les boutons
        messageBtn.disabled = false;
        if (answerBtn) answerBtn.disabled = false;
        if (likeBtn) likeBtn.disabled = false;

        // On cache tous les contenus
        messageContent.style.display = "none";
        answerContent.style.display = "none";
        likeContent.style.display = "none";

        // On active le bouton cliqué et affiche le contenu correspondant
        btn.disabled = true;
        content.style.display = "block";
    }

    // Ajout des écouteurs d'événements pour chaque bouton
    messageBtn.addEventListener("click", function() {
        switchContent(this, messageContent);
    });

    if (answerBtn) {
        answerBtn.addEventListener("click", function() {
            switchContent(this, answerContent);
        });
    }

    if (likeBtn) {
        likeBtn.addEventListener("click", function() {
            switchContent(this, likeContent);
        });
    }
});