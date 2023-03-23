<script>
    // fonction pour ouvrir la fenêtre
    function openWindow(window) {
        document.getElementById(window).style.display = "inline-block";
    }
    // fonction pour fermer la fenêtre
    function closeWindow(window) {
        document.getElementById(window).style.display = "none";
    }

    function closeWindowToNewDestination(window, url) {
        document.getElementById(window).style.display = "none";
        location.href = url;
    }
</script>