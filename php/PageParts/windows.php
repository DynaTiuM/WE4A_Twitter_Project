<script>
    // fonction pour ouvrir la fenêtre
    function openWindow(window, type = 'inline-block') {
        document.getElementById(window).style.display = type;

        if(window === 'display-pet') document.getElementById("display-type").style.display = "none";
        else document.getElementById("display-pet").style.display = "none";

        document.getElementById("map-container").style.display = "none";
    }
    // fonction pour fermer la fenêtre
    function closeWindow(window) {
        document.getElementById(window).style.display = "none";
    }
    function openWindowByNavigation(window) {
        document.getElementById(window).style.display = "none";
        document.getElementById(window).style.display = "inline-block";
    }


    function closeWindowToNewDestination(window, url) {
        document.getElementById(window).style.display = "none";
        location.href = url;
    }
</script>