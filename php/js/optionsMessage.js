/**
 * Méthode permettant d'afficher la petite pop-up de modification d'un message
 *
 * @param messageId
 */
function displayModification(messageId) {
    // On récupère l'id du message, situé dans une division options-dropdown + l'id du message
    // Ainsi, chaque message possède un options-dropdown-messageId différent, permettant de les discerner et savoir lequel mettre à jour
    var dropdown = document.getElementById("options-dropdown-" + messageId);
    // Finalement, on affiche donc la petite pop-up dropdown de la modification de message :
    if (dropdown.style.display === "block") {
        dropdown.style.display = "none";
    } else {
        dropdown.style.display = "block";
    }
}

/**
 * Méthode permettant d'afficher la pop-up de modification d'un message
 *
 * @param messageId
 * @param currentContent
 */
function popUpUpdateMessage(messageId, currentContent) {
    // On récupère l'id de la division permettant l'affichage de la pop-up
    var popup = document.getElementById("updateMessagePopup");
    // Et on l'affiche donc grâce à la propriété display = block
    popup.style.display = "block";
    // On récupère les informations du message grâce aux paramètres de la fonction, et on les ajoute dans le formulaire de la pop-up :
    document.getElementById("messageIdToUpdate").value = messageId;
    document.getElementById("newContent").value = currentContent;
}

/**
 * Fonction passerelle permettant de modifier un message
 *
 * @param messageId
 */
function editMessage(messageId) {
    // On récupère le contenu actuel du message
    var currentContent = document.getElementById("message-" + messageId).innerText;

    // Et on affiche donc la popup avec le formulaire de modification du message, vu plus haut
    popUpUpdateMessage(messageId, currentContent);
}

/**
 * Fonction permettant de mettre à jour le message sur la base de données, par requete AJAX
 *
 * @param messageId
 * @param newContent
 */
function updateMessage(messageId, newContent) {
    // Encore une fois, on récupère l'id du message
    const messageElement = document.getElementById("message-" + messageId);
    // Puis le nouveau contenu grâce à la propriété innerHTML qui point sur l'élément de l'id du message
    messageElement.innerHTML = newContent;

    // On crée donc une instance de la classe XMLHttpRequest
    const xhr = new XMLHttpRequest();
    // On initialise la requete HTTP à envoyer sur le serveur. On ouvre donc le fichier updateMessage.php avec une methode POST.
    xhr.open("POST", "../PageParts/updateMessage.php", true);

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        // On vérifie que la requete est terminée et que le status HTTP est égal à 200, c'est à dire que l'opération s'est bien réalisé
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            // Si c'est le cas, cela signifie que la requete a bien été envoyée, on l'informe par console javascript :
            console.log("Message mis à jour dans la base de données");
        }
    };
    // On envoie l'information HTTP au serveur avec les données que l'on souhaite (ici messageID et le nouveau contenu du message)
    // le encodeURIComponent est utilisé pour éviter des problemes de caractères spéciaux présents dans le message
    // Grace à ces informations, il est donc possible de modifier le message à la base de données grâce à la methode post, sans raffraichir la page
    xhr.send("messageId=" + messageId + "&newContent=" + encodeURIComponent(newContent));
}

/**
 * Fonction permettant de demander l'envoi de modification de message
 *
 */
function submitUpdateMessageForm() {
    // On récupère l'id du message et le nouveau contenu grâce aux valeurs des ids des divisions respectives
    var messageId = document.getElementById("messageIdToUpdate").value;
    var newContent = document.getElementById("newContent").value;

    // On appelle ENFIN la fonction updateMessage pour mettre à jour le message avec le nouveau contenu
    updateMessage(messageId, newContent);

    // On n'oublie pas de fermer la popup
    document.getElementById("updateMessagePopup").style.display = "none";
}


/**
 * Fonction permettant de supprimer un message
 *
 * @param messageId
 */
function deleteMessage(messageId) {
    // Le procédé est exactement le meme que la fonction updateMessage()
    const xhr = new XMLHttpRequest();
    // Excepté le fait qu'ici la méthode POST pointe sur le fichier deleteMessage.php
    xhr.open("POST", "../PageParts/deleteMessage.php", true);

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            console.log("Message supprimé de la base de données");
        }
    };
    xhr.send("messageId=" + messageId);

    const messageElement = document.getElementById("message-container-" + messageId);
    messageElement.style.display = "none";

}
