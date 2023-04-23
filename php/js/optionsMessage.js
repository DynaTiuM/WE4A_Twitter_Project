
function displayModification(messageId) {
    var dropdown = document.getElementById("options-dropdown-" + messageId);
    if (dropdown.style.display === "block") {
        dropdown.style.display = "none";
    } else {
        dropdown.style.display = "block";
    }
}

window.onclick = function (event) {
    if (event.target.matches(".options-button")) {
        return;
    }

    const dropdowns = document.querySelectorAll(".options-dropdown");
    dropdowns.forEach((dropdown) => {
        dropdown.style.display = "none";
    });
};


function popUpUpdateMessage(messageId, currentContent) {
    var popup = document.getElementById("updateMessagePopup");
    popup.style.display = "block";
    document.getElementById("messageIdToUpdate").value = messageId;
    document.getElementById("newContent").value = currentContent;
}

function editMessage(messageId) {
    // Récupérer le contenu actuel du message
    var currentContent = document.getElementById("message-" + messageId).innerText;

    // Afficher la popup avec le formulaire de modification du message
    popUpUpdateMessage(messageId, currentContent);
}

function updateMessage(messageId, newContent) {
    const messageElement = document.getElementById("message-" + messageId);
    messageElement.innerHTML = newContent;

    // Creation of an instance of XMLHttpRequest
    const xhr = new XMLHttpRequest();
    // Initialisation of the HTTP request to send to the server. We open the updateMessage.php file with the POST method, and we choose it as asynchronous
    xhr.open("POST", "../PageParts/updateMessage.php", true);

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        // We verify if the request is finished and if the HTTP status is equal to 200 (ok)
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            // If this is the case, it means that the request has been sent successfully.
            console.log("Message mis à jour dans la base de données");
        }
    };
    // We send the HTTP information to the server with the datas we want (messageId & the content).
    // The encodeURIComponent is used to avoid special characters' problem
    // Thanks to these information, we'll be able to modify a message on database thanks to post method
    xhr.send("messageId=" + messageId + "&newContent=" + encodeURIComponent(newContent));
}

function submitUpdateMessageForm() {
    var messageId = document.getElementById("messageIdToUpdate").value;
    var newContent = document.getElementById("newContent").value;

    // Appeler la fonction updateMessage pour mettre à jour le message avec le nouveau contenu
    updateMessage(messageId, newContent);

    // Fermer la popup
    document.getElementById("updateMessagePopup").style.display = "none";
}


function deleteMessage(messageId) {
    // Creation of an instance of XMLHttpRequest
    const xhr = new XMLHttpRequest();
    // Initialisation of the HTTP request to send to the server. We open the updateMessage.php file with the POST method, and we choose it as asynchronous
    xhr.open("POST", "../PageParts/deleteMessage.php", true);

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        // We verify if the request is finished and if the HTTP status is equal to 200 (ok)
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            // If this is the case, it means that the request has been sent successfully.
            console.log("Message supprimé de la base de données");
        }
    };
    // We send the HTTP information to the server with the datas we want (messageId & the content).
    // The encodeURIComponent is used to avoid special characters' problem
    // Thanks to these information, we'll be able to modify a message on database thanks to post method
    xhr.send("messageId=" + messageId);


    const messageElement = document.getElementById("message-container-" + messageId);
    messageElement.style.display = "none";

}
