<script>
    // fonction pour ouvrir la fenêtre
    function openWindow(window) {
        document.getElementById(window).style.display = "inline-block";
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