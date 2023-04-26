/**
 * Fonction permettant d'ouvrir tout type de pop-up
 *
 * @param window
 * @param type
 */
function openWindow(window, type = 'inline-block') {
    // On affiche la pop-up, qui à l'origine était en style = none grâce à la propriété suivante JavaScript en la mettant en inline-block :
    document.getElementById(window).style.display = type;
    // On récupère l'id de la division de la fenetre :
    const windowElement = document.getElementById(window);
    // Petite vérification réalisée dans le cadre de la réinitialisation de mot de passe :
    if (windowElement) {
        // S'il s'agit de l'affichage de la division de mot de passe oublié :
        if(window === 'lost-password'){
            // Il est nécessaire de rendre la division de connexion invisible
            document.getElementById("connection").style.display = "none";
        }
        // On souhaite afficher seulement 1 option à chaque fois pour la création du message :
        if(window === 'display-pet') document.getElementById("display-type").style.display = "none";
        else if(document.getElementById("display-pet")) document.getElementById("display-pet").style.display = "none";
        if(document.getElementById("map-container")) document.getElementById("map-container").style.display = "none";
    } else {
        console.error(`Element avec l'ID ${window} non trouvé`);
    }
}

// Cette fonction permet de ne plus afficher aucun bouton de modification de message lorsque l'on ouvre une pop-up de modification de message
window.onclick = function (event) {
    if (event.target.matches(".options-button")) {
        return;
    }

    // On récupère toutes les dropdowns
    const dropdowns = document.querySelectorAll(".options-dropdown");
    // Et pour chaque, on met leur visibilité en "none"
    dropdowns.forEach((dropdown) => {
        dropdown.style.display = "none";
    });
};

/**
 * Fonction permettant d'ouvrir la pop-up de code de réinitialisation de mot de passe
 *
 * @param window
 */
function openCodeWindow(window) {
    // On récupère l'id de la division et on ajoute son style en "block"
    document.getElementById(window).style.display = "block";
}


/**
 * Fonction qui permet de fermer n'importe quelle pop-up en fonction de son id de division
 *
 * @param window
 */
function closeWindow(window) {
    // On met simplement la division en display = "none"
    document.getElementById(window).style.display = "none";
}

/**
 * Fonction permettant de fermer une pop-up et de rediriger l'utilisateur vers une nouvelle destination
 *
 * @param window
 * @param url
 */
function closeWindowToNewDestination(window, url) {
    document.getElementById(window).style.display = "none";
    location.href = url;
}