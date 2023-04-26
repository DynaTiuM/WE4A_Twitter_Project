
// On s'assure ici que tout le contenu ai chargé avant d'appeler le javascript, pour éviter de pointer sur des éléments null
document.addEventListener("DOMContentLoaded", function() {
    // On récupère tous les boutons grâce à leur ID
    const messageBtn = document.getElementById("message-button");
    const answerBtn = document.getElementById("answer-button");
    const likeBtn = document.getElementById("like-button");

    // On récupère également tous les contenus grâce aux ids des divisions
    const messageContent = document.getElementById("message-content");
    const answerContent = document.getElementById("answer-content");
    const likeContent = document.getElementById("like-content");

    /**
     * Fonction qui switch le contenu des messages du profil
     *
     * @param btn
     * @param content
     */
    function switchContent(btn, content) {
        // On commence tout d'abord par désactiver tous les boutons
        messageBtn.disabled = false;
        if (answerBtn) answerBtn.disabled = false;
        if (likeBtn) likeBtn.disabled = false;

        // Ainsi que cacher tous les contenus
        messageContent.style.display = "none";
        answerContent.style.display = "none";
        likeContent.style.display = "none";

        // C'est une manière très simple pour switch les contenus, on se contente tout simplement de tout cacher, puis d'afficher celui qui a été cliqué

        // Tout simplement, on récupère le bouton mis en paramètres, et on le désactive (pour ne pas pouvoir recliquer desuss alors qu'on se trouve déjà dans la section)
        // On fait de meme en affichant le contenu en style block
        btn.disabled = true;
        content.style.display = "block";
    }

    // On ajoute ainsi les listeners de chaque bouton, permettant d'appeler la fonction switchContent à chaque fois que l'on clique sur un bouton d'une catégorie
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