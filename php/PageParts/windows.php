<script>
    // fonction pour ouvrir la fenêtre

    function openWindow(window, type = 'inline-block') {
        document.getElementById(window).style.display = type;

        if(window === 'lost-password'){
            document.getElementById("connexion").style.display = "none";
        }
        if(window === 'display-pet') document.getElementById("display-type").style.display = "none";
        else if(document.getElementById("display-pet")) document.getElementById("display-pet").style.display = "none";
        if(document.getElementById("map-container")) document.getElementById("map-container").style.display = "none";

    }
    // fonction pour fermer la fenêtre
    function closeWindow(window) {
        document.getElementById(window).style.display = "none";
    }

    function openWindowByNavigation(window) {

        var content = document.getElementById("new-message-content");

        content.style.display = "none";
        document.getElementById(window).style.display = "inline-block";
    }

    function closeWindowToNewDestination(window, url) {
        document.getElementById(window).style.display = "none";
        location.href = url;
    }
</script>